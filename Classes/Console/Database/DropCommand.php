<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console\Database;

use Cundd\PersistentObjectStore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to create a new database
 *
 * @package Cundd\PersistentObjectStore\Console
 */
class DropCommand extends AbstractCommand {
	/**
	 * Configure the command
	 */
	protected function configure() {
		$this
			->setName('database:drop')
			->setDescription('Drop the given database')
			->addArgument(
				'identifier',
				InputArgument::REQUIRED,
				'Unique name of the database to remove'
			)
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
	protected function execute(InputInterface $input, OutputInterface $output) {
		$databaseIdentifier = $input->getArgument('identifier');
		$this->coordinator->dropDatabase($databaseIdentifier);
		$output->writeln(sprintf('<info>Dropped database %s</info>', $databaseIdentifier));
	}
}