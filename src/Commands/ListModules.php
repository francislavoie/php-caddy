<?php

namespace PhpCaddy\Commands;

use PhpCaddy\Core;
use PhpCaddy\Module;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListModules extends Command
{
    protected static $defaultName = 'list-modules';

    protected function configure()
    {
        $this
            ->setDescription("Lists all the registered modules");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $table->setHeaders(["Name", "ID"]);

        /** @var Module $module */
        foreach (Core::getAllModules() as $module) {
            $info = $module::module();

            $table->addRow([$info->name(), $info->id()]);

            if (! $output->isVerbose()) {
                $output->writeln($info->id());
            }
        }

        if ($output->isVerbose()) {
            $table->render();
        }
        return Command::SUCCESS;
    }
}
