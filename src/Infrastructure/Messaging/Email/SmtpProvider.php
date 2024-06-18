<?php

namespace App\Infrastructure\Messaging\Email;

use App\Domain\Message\Message;
use App\Domain\Service\ServiceProviderInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class SmtpProvider implements ServiceProviderInterface
{
    private MailerInterface $mailer;
    private string $fromEmail;
    private MessageBusInterface $bus;
    private int $maxRetries;
    private int $delay;
    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, MessageBusInterface $bus, string $fromEmail, int $maxRetries, int $delay, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->bus = $bus;
        $this->fromEmail = $fromEmail;
        $this->maxRetries = $maxRetries;
        $this->delay = $delay;
        $this->logger = $logger;
    }

    public function send(Message $message): bool
    {
        $email = (new Email())
            ->from($this->fromEmail)
            ->to($message->getRecipient())
            ->subject('message')
            ->text($message->getContent());

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            $retryCount = $message->getRetryCount();
            $this->logger->error('Failed to send email', [
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
        $email = (new Email())
            ->from($this->fromEmail)
            ->to('nonexistent@example.com')
            ->subject('Availability Check')
            ->text('This is a test to check transport availability.');

        try {
            $this->mailer->send($email);

            return true;
        } catch (TransportExceptionInterface $e) {
            error_log($e->getMessage());

            return false;
        }
    }
}
