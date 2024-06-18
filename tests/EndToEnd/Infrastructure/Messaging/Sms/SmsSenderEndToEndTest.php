<?php

namespace App\Tests\EndToEnd\Infrastructure\Messaging\Sms;

use App\Infrastructure\Messaging\Sms\SmsSender;
use App\Domain\Message\Message;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Twilio\Rest\Client;

class SmsSenderEndToEndTest extends TestCase
{
    private $client;
    private $logger;
    private $smsSender;
    private $fromNumber = '+15513822280';
    private int $maxRetries = 3;
    private int $delay = 1000;

    protected function setUp(): void
    {
        $this->client = new Client('TWILIO_SID', 'TWILIO_PASSWORD');
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->busMock = $this->createMock(MessageBusInterface::class);
        $this->smsSender = new SmsSender($this->client, $this->fromNumber, $this->logger, $this->busMock, $this->maxRetries, $this->delay);
    }

    public function testSendSmsSuccessfully()
    {
        // This should ideally be a sandbox number for Twilio testing
        $message = new Message('0987654321', 'End-to-end test message');

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('SMS sent successfully'));

        $this->busMock->expects($this->any())
            ->method('dispatch')
            ->willReturnCallback(function ($message, $stamps) {
                return new Envelope($message, $stamps);
            });

        $result = $this->smsSender->send($message);

        $this->assertTrue($result);
    }

    public function testSendSmsFailure()
    {
        $message = new Message('invalid_number', 'End-to-end test message');

        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Failed to send SMS'));

        $this->busMock->expects($this->any())
            ->method('dispatch')
            ->willReturnCallback(function ($message, $stamps) {
                return new Envelope($message, $stamps);
            });

        $result = $this->smsSender->send($message);

        $this->assertFalse($result);
    }
}
