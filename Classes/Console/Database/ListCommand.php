<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console\Database;

use Cundd\PersistentObjectStore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list databases
 *
 * @package Cundd\PersistentObjectStore\Console
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
            ->setDescription('List available databases')
//			->addArgument(
//				'name',
//				InputArgument::OPTIONAL,
//				'Who do you want to greet?'
//			)
//			->addOption(
//				'yell',
//				null,
//				InputOption::VALUE_NONE,
//				'If set, the task will yell in uppercase letters'
//			)
        ;
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
