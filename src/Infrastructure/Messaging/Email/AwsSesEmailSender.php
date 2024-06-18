<?php

namespace App\Infrastructure\Messaging\Email;

use App\Domain\Message\Message;
use App\Domain\Service\ServiceProviderInterface;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use Psr\Log\LoggerInterface;

class AwsSesEmailSender implements ServiceProviderInterface
{
    private SesClient $client;
    private string $senderEmail;
    private LoggerInterface $logger;

    public function __construct(SesClient $client, string $senderEmail, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->senderEmail = $senderEmail;
        $this->logger = $logger;
    }

    public function send(Message $message): bool
    {
        try {
            $result = $this->client->sendEmail([
                'Destination' => [
                    'ToAddresses' => [$message->getRecipient()],
                ],
                'Message' => [
                    'Body' => [
                        'Text' => [
                            'Data' => $message->getContent(),
                        ],
                    ],
                    'Subject' => [
                        'Data' => 'Your Notification',
                    ],
                ],
                'Source' => $this->senderEmail,
            ]);
            $this->logger->info('Email sent successfully', ['result' => $result]);

            return true;
        } catch (AwsException $e) {
            $this->logger->error('Failed to send email', [
                'error' => $e->getMessage(),
                'recipient' => $message->getRecipient(),
                'content' => $message->getContent(),
            ]);

            return false;
        } catch (\Exception $e) {
            $this->logger->critical('An unexpected error occurred during email sending', [
                'error' => $e->getMessage(),
                'recipient' => $message->getRecipient(),
                'content' => $message->getContent(),
            ]);

            return false;
        }
    }

    public function isAvailable(): bool
    {
        try {
            $this->client->getSendQuota();

            return true;
        } catch (AwsException $e) {
            error_log($e->getMessage());

            return false;
        }
    }
}
