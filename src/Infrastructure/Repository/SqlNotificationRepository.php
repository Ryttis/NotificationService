<?php

namespace App\Infrastructure\Repository;

use App\Domain\Model\Notification;
use App\Domain\Repository\NotificationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class SqlNotificationRepository implements NotificationRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Notification::class);
    }

    public function save(Notification $notification): void
    {
        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Notification
    {
        return $this->repository->find($id);
    }

    public function delete(Notification $notification): void
    {
        $this->entityManager->remove($notification);
        $this->entityManager->flush();
    }
}
