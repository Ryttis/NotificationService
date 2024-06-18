<?php

namespace App\Application\Service;

use App\Domain\Model\Notification;
use App\Domain\Service\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;

class NotificationService
{
    /**
     * @var ServiceLocator<ServiceLocator>
     */
    private ServiceLocator $locator;
    /**
     * @var array<string, array{enabled: bool, class: string|array<string>}>
     */
    private array $channels;

    /**
     * NotificationService constructor.
     */
    public function __construct(ServiceLocator $locator)
    {
        $this->locator = $locator;
        $this->channels = require __DIR__.'/../../../config/channels.php';
    }

    public function sendNotification(Notification $notification): void
    {
        foreach ($this->channels['channels'] as $channel => $config) {
            if ($config['enabled']) {
                /** @var ServiceProviderInterface $sender */
                $sender = $this->locator->get($channel);
                $sender->send($notification->getMessage());
            }
        }
    }
}
