<?php

return [
    'channels' => [
        'email_smtp' => [
            'enabled' => true,
            'class' => App\Infrastructure\Messaging\Email\SmtpProvider::class,
        ],
        'email_aws' => [
            'enabled' => false,
            'class' => App\Infrastructure\Messaging\Email\AwsSesEmailSender::class,
        ],
        'sms' => [
            'enabled' => false,
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
