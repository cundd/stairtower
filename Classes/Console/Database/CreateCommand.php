<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Database;

use Cundd\Stairtower\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to create a new database
 */
class CreateCommand extends AbstractCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('database:create')
            ->setDescription('Create a new database')
            ->addArgument(
                'identifier',
                InputArgument::REQUIRED,
                'Unique name of the database to create'
            )
            ->addArgument(
                'options',
                InputArgument::IS_ARRAY,
                'Options for the new database (separate with a space)'
            );
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
        $databaseIdentifier = $input->getArgument('identifier');
        $options = $input->getArgument('options');
        $database = $this->coordinator->createDatabase($databaseIdentifier, $options);
        if ($database) {
            $output->writeln(sprintf('<info>Created database %s</info>', $databaseIdentifier));
        }
    }
}
