<?php

namespace App\Infrastructure\Messaging\Facebook;

use App\Domain\Message\Message;
use App\Domain\Service\ServiceProviderInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class FacebookMessengerSender implements ServiceProviderInterface
{
    private Client $client;
    private string $pageAccessToken;

    public function __construct(Client $client, string $pageAccessToken)
    {
        $this->client = $client;
        $this->pageAccessToken = $pageAccessToken;
    }

    /**
     * Send a message to a recipient via Facebook Messenger.
     *
     * @param Message $message the message to send
     *
     * @return bool
     *
     * @throws GuzzleException
     */
    public function send(Message $message): bool
    {
        $url = 'https://graph.facebook.com/v10.0/me/messages?access_token='.$this->pageAccessToken;

        $payload = [
            'recipient' => ['id' => $message->getRecipient()],
            'message' => ['text' => $message->getContent()],
        ];

        $response = $this->client->post($url, [
            'json' => $payload,
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if (!$result) {
            return false;
        }

        return true;
    }

    public function isAvailable(): bool
    {
        return true;
    }
}
