<?php

namespace App\Domain\Message;

class Message
{
    private string $recipient;

    private string $title;
    private string $content;
    private int $retryCount = 0;

    public function __construct(string $recipient, string $content)
    {
        $this->recipient = $recipient;
        $this->content = $content;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRetryCount(): int
    {
        return $this->retryCount;
    }

    public function incrementRetryCount(): void
    {
        ++$this->retryCount;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
