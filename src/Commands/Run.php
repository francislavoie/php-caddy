<?php

namespace PhpCaddy\Commands;

use PhpCaddy\Core;
use PhpCaddy\Modules\Http\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    protected static $defaultName = 'run';

    protected function configure()
    {
        $this
            ->setDescription("Runs the app!")
            ->setHelp("The entrypoint for running the app!");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var App $http */
        $http = Core::getModule('http');

        $output->writeLn("Server running at http://127.0.0.1:8080");

        $http->start();

        return Command::SUCCESS;
    }
}
