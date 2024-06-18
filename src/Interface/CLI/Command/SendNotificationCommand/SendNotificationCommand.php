<?php

namespace App\Interface\CLI\Command\SendNotificationCommand;

use App\Application\Service\NotificationService;
use App\Domain\Message\Message;
use App\Domain\Model\Notification;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendNotificationCommand extends Command
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:send-notification')
            ->setDescription('Sends a notification to a specified recipient.')
            ->addArgument('recipient', InputArgument::REQUIRED, 'The recipient of the notification.')
            ->addArgument('message', InputArgument::REQUIRED, 'The message content of the notification.')
            ->addArgument('senderType', InputArgument::REQUIRED, 'The message sender type.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $recipient = $input->getArgument('recipient');
        $messageContent = $input->getargument('message');
        $senderType = $input->getargument('senderType');

        $message = new Message($recipient, $messageContent);
        $notification = new Notification($message);

        try {
            $this->notificationService->sendNotification($notification, $senderType);
            $output->writeln('Notification sent successfully to: '.$recipient);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Failed to send notification: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
