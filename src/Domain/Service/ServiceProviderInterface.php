<?php

namespace App\Domain\Service;

use App\Domain\Message\Message;

interface ServiceProviderInterface
{
    public function send(Message $message): bool;

    public function isAvailable(): bool;
}
