<?php

namespace App\Domain\Model;

use App\Domain\Enum\NotificationStatus;
use App\Domain\Message\Message;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Infrastructure\Repository\SqlNotificationRepository")]
#[ORM\Table(name: 'notifications')]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private ?int $id;
    #[ORM\Column(type: 'text')]
    private Message $message;
    #[ORM\Column(type: 'text')]
    private NotificationStatus $status;

    public function __construct(Message $message, ?int $id = null)
    {
        $this->id = $id;
        $this->message = $message;
        $this->status = NotificationStatus::Pending;
    }

    public function markAsSent(): void
    {
        $this->status = NotificationStatus::Sent;
    }

    public function markAsFailed(): void
    {
        $this->status = NotificationStatus::Failed;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
