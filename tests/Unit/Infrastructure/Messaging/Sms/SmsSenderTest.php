<?php

namespace App\Tests\Unit\Infrastructure\Messaging\Sms;

use App\Infrastructure\Messaging\Sms\SmsSender;
use App\Domain\Message\Message;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Twilio\Rest\Client;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Symfony\Component\Messenger\MessageBusInterface;

class SmsSenderTest extends TestCase
{
    private $clientMock;
    private $messagesMock;
    private $messageInstanceMock;
    private $loggerMock;
    private $busMock;
    private $smsSender;
    private $fromNumber = '1234567890';
    private int $maxRetries = 3;
    private int $delay = 1000;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->messagesMock = $this->createMock(MessageList::class);
        $this->clientMock->expects($this->any())
            ->method('__get')
            ->with('messages')
            ->willReturn($this->messagesMock);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->busMock = $this->createMock(MessageBusInterface::class);
        $this->messageInstanceMock = $this->createMock(MessageInstance::class);
        $this->smsSender = new SmsSender($this->clientMock, $this->fromNumber, $this->loggerMock, $this->busMock, $this->maxRetries, $this->delay);
    }

    public function testSendSmsSuccessfully()
    {
        $messageMock = $this->createMock(Message::class);
        $messageMock->method('getRecipient')->willReturn('0987654321');
        $messageMock->method('getContent')->willReturn('Test message');

        $this->messageInstanceMock->sid = 'SM1234567890';

        $this->messagesMock->method('create')->willReturn($this->messageInstanceMock);

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with('SMS sent successfully', ['sid' => 'SM1234567890']);

        $result = $this->smsSender->send($messageMock);

        $this->assertTrue($result);
    }

    public function testSendSmsFailure()
    {
        $messageMock = $this->createMock(Message::class);
        $messageMock->method('getRecipient')->willReturn('0987654321');
        $messageMock->method('getContent')->willReturn('Test message');

        $this->messagesMock->method('create')->willThrowException(new \Exception('Sending failed'));

        $this->loggerMock->expects($this->exactly(1))
            ->method('error')
            ->with(
                $this->stringContains('Failed to send SMS'),
                $this->callback(function($context) {
                    return isset($context['error']) && $context['error'] === 'Sending failed';
                })
            );

        $this->busMock->expects($this->exactly(1))
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(Message::class),
                $this->callback(function ($stamps) {
                    $delayStamp = $stamps[0];
                    return $delayStamp instanceof DelayStamp && $delayStamp->getDelay() === 1000;
                })
            )
            ->willReturnCallback(function ($message, $stamps) {
                return new Envelope($message, $stamps);
            });

        // Call the method and assert the final result
        $result = $this->smsSender->__invoke($messageMock);
        $this->assertFalse($result);
    }
    public function testSendSmsSuccessfullyAfterRetries()
    {
        $messageMock = $this->createMock(Message::class);
        $messageMock->method('getRecipient')->willReturn('0987654321');
        $messageMock->method('getContent')->willReturn('Test message');

        $this->messageInstanceMock->sid = 'SM1234567890';

        // Set up the message sending to fail twice and succeed on the third attempt
        $this->messagesMock->expects($this->exactly(3))
            ->method('create')
            ->willReturnOnConsecutiveCalls(
                $this->throwException(new \Exception('Sending failed')),
                $this->throwException(new \Exception('Sending failed')),
                $this->messageInstanceMock
            );

        // Mocking logging for failure and success
        $this->loggerMock->expects($this->exactly(2))
            ->method('error')
            ->with(
                $this->stringContains('Failed to send SMS'),
                $this->callback(function($context) {
                    return isset($context['error']) && $context['error'] === 'Sending failed';
                })
            );

        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with('SMS sent successfully', ['sid' => 'SM1234567890']);

        // Mock bus dispatch with delay stamps and verify the arguments
        $this->busMock->expects($this->exactly(2))
            ->method('dispatch')
            ->with(
                $this->callback(function ($message) {
                    return $message instanceof Message;
                }),
                $this->callback(function ($stamps) {
                    if (!is_array($stamps) || count($stamps) !== 1) {
                        return false;
                    }
                    $stamp = $stamps[0];
                    return $stamp instanceof DelayStamp && $stamp->getDelay() === 1000;
                })
            )
            ->willReturnCallback(function ($message, $stamps) {
                return new Envelope($message, $stamps);
            });

        // Simulate message retries
        $this->smsSender->send($messageMock);
        $this->smsSender->send($messageMock);
        // The final call should return true as it succeeds
        $result = $this->smsSender->send($messageMock);

        $this->assertTrue($result);
    }
}
