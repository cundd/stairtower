<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Database;

use Cundd\Stairtower\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list databases
 */
class ListCommand extends AbstractCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('database:list')
            ->setDescription('List available databases');
    }

    /**
     * Execute the command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $databases = $this->coordinator->listDatabases();
        if ($databases) {
            $output->writeln('<info>Databases:</info>');
            foreach ($databases as $databaseIdentifier) {
                $output->writeln($databaseIdentifier);
            }
        } else {
            $output->writeln('<info>No databases found</info>');
        }
    }
}
