<?php

namespace App\Tests\Integration\Infrastructure\Messaging\Sms;

use App\Infrastructure\Messaging\Sms\SmsSender;
use App\Domain\Message\Message;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Twilio\Rest\Client;

class SmsSenderIntegrationTest extends TestCase
{
    private $client;
    private $logger;
    private $smsSender;
    private $fromNumber = '+15513822280';
    private int $maxRetries = 3;
    private int $delay = 1000;

    protected function setUp(): void
    {
        $this->client = new Client('TWILIO_ACCOUNT_SID', 'TWILIO_AUTH_TOKEN');
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->busMock = $this->createMock(MessageBusInterface::class);
        $this->smsSender = new SmsSender($this->client, $this->fromNumber, $this->logger, $this->busMock, $this->maxRetries, $this->delay);
    }

    public function testSendSmsSuccessfully()
    {
        $message = new Message('+37062071053', 'Integration test message');

        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('SMS sent successfully'));

        $this->busMock->expects($this->any())
            ->method('dispatch')
            ->willReturnCallback(function ($message, $stamps) {
                return new Envelope($message, $stamps);
            });

        // Add debug logging before sending the SMS
        echo "Sending SMS to " . $message->getRecipient() . " with content: " . $message->getContent() . "\n";

        $result = $this->smsSender->send($message);

        // Add debug logging after sending the SMS
        echo "Result of sending SMS: " . ($result ? 'Success' : 'Failure') . "\n";

        $this->assertTrue($result);
    }
}
