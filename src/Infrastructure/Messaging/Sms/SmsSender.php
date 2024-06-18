<?php

namespace App\Infrastructure\Messaging\Sms;

use App\Domain\Message\Message;
use App\Domain\Service\ServiceProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Twilio\Rest\Client;

#[AsMessageHandler]
class SmsSender implements ServiceProviderInterface
{
    private Client $client;
    private string $fromNumber;
    private LoggerInterface $logger;
    private MessageBusInterface $bus;
    private int $maxRetries;
    private int $delay;

    public function __construct(Client $client, string $fromNumber, LoggerInterface $logger, MessageBusInterface $bus, int $maxRetries, int $delay)
    {
        $this->client = $client;
        $this->fromNumber = $fromNumber;
        $this->logger = $logger;
        $this->bus = $bus;
        $this->maxRetries = $maxRetries;
        $this->delay = $delay;
    }

    public function __invoke(Message $message): bool
    {
        return $this->send($message);
    }

    public function send(Message $message): bool
    {
        try {
            $result = $this->client->messages->create(
                $message->getRecipient(),
                [
                    'from' => $this->fromNumber,
                    'body' => $message->getContent(),
                ]
            );

            $this->logger->info('SMS sent successfully', ['sid' => $result->sid]);

            return true;
        } catch (\Exception $e) {
            $retryCount = $message->getRetryCount();
            $this->logger->error('Failed to send SMS', [
                'error' => $e->getMessage(),
                'recipient' => $message->getRecipient(),
                'content' => $message->getContent(),
                'retry_count' => $retryCount,
            ]);
            if ($retryCount < $this->maxRetries) {
                $message->incrementRetryCount();
                $this->bus->dispatch($message, [new DelayStamp($this->delay)]);
            }

            return false;
        }
    }

    public function isAvailable(): bool
    {
        return false;
    }
}
