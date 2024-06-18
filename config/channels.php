<?php

return [
    'channels' => [
        'email' => [
            'enabled' => false,
            'class' => [
                App\Infrastructure\Messaging\Email\SendgridProvider::class,
                App\Infrastructure\Messaging\Email\SmtpProvider::class,
                App\Infrastructure\Messaging\Email\AwsSesEmailSender::class,
            ],
        ],
        'sms' => [
            'enabled' => true,
            'class' => App\Infrastructure\Messaging\Sms\SmsSender::class,
        ],
        'facebook' => [
            'enabled' => false,
            'class' => App\Infrastructure\Messaging\Facebook\FacebookMessengerSender::class,
        ],
        'push' => [
            'enabled' => false,
            'class' => App\Infrastructure\Messaging\Push\PushSender::class,
        ],
    ],
];
