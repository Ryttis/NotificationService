<?php

namespace App\Interface\CLI\Command\CheckEnvVariables;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CheckEnvVariablesCommand extends Command
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        parent::__construct();
        $this->params = $params;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:check-env-vars')
            ->setDescription('Checks set env variables.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('MESSENGER_MAX_RETRIES: '.$this->params->get('env(MESSENGER_MAX_RETRIES)'));
        $output->writeln('MESSENGER_DELAY: '.$this->params->get('env(MESSENGER_DELAY)'));
        $output->writeln('MESSENGER_MULTIPLIER: '.$this->params->get('env(MESSENGER_MULTIPLIER)'));

        return Command::SUCCESS;
    }
}
