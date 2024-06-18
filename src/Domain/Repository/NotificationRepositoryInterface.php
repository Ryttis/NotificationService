<?php

namespace App\Domain\Repository;

use App\Domain\Model\Notification;

interface NotificationRepositoryInterface
{
    /**
     * Saves a Notification entity.
     */
    public function save(Notification $notification): void;

    /**
     * Retrieves a Notification by its ID.
     */
    public function findById(int $id): ?Notification;

    /**
     * Deletes a Notification from the repository.
     */
    public function delete(Notification $notification): void;
}
