<?php

namespace App\Infrastructure\Messaging\Push;

use App\Domain\Message\Message;
use App\Domain\Service\ServiceProviderInterface;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Psr\Log\LoggerInterface;

class PushSender implements ServiceProviderInterface
{
    private Messaging $messaging;
    private LoggerInterface $logger;

    public function __construct(string $firebaseConfigPath, LoggerInterface $logger)
    {
        $factory = (new Factory())
            ->withServiceAccount($firebaseConfigPath);

        $this->messaging = $factory->createMessaging();
        $this->logger = $logger;
    }

    /**
     * @throws MessagingException
     * @throws FirebaseException
     */
    public function send(Message $message): bool
    {
        $deviceToken = $message->getRecipient();
        $result = CloudMessage::new()
            ->withNotification(['title' => 'message', 'body' => $message->getContent()])
            ->withData([]);

        try {
            $this->messaging->send($result->withChangedTarget('token', $deviceToken));

            return true;
        } catch (\Exception $e) {
            $this->logger->error('Failed to send SMS', [
                'error' => $e->getMessage(),
                'recipient' => $message->getRecipient(),
                'content' => $message->getContent(),
            ]);

            return false;
        }
    }

    public function isAvailable(): bool
    {
        return true;
    }
}
