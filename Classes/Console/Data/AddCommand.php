<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console\Data;

use Cundd\PersistentObjectStore\Console\AbstractCommand;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list data
 *
 * @package Cundd\PersistentObjectStore\Console
 */
class AddCommand extends AbstractDataCommand {
	/**
	 * Configure the command
	 */
	protected function configure() {
		$this
			->setName('data:add')
			->setDescription('Add an entry to the database')
			->addArgument(
				'database',
				InputArgument::REQUIRED,
				'Unique name of the database to search in'
			)
			->addArgument(
				'content',
				InputArgument::REQUIRED,
				'JSON encoded data to add to the database'
			)
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
		$database = $this->findDatabaseInstanceFromInput($input);

		/** @var DataInterface $dataInstance */
		$dataInstance = $this->serializer->unserialize($input->getArgument('content'));
		if (!$dataInstance) {
			$output->writeln('<error>Could not create object</error>');
		}
		$database->add($dataInstance);
		$objectIdentifier = $dataInstance->getId();

		$this->coordinator->commitDatabase($database);

		if ($database->contains($dataInstance)) {
			$output->writeln(sprintf('<info>Object with ID %s was add to database %s</info>', $objectIdentifier, $database->getIdentifier()));
		} else {
			$output->writeln(sprintf('<info>Object with ID %s could not be add to database %s</info>', $objectIdentifier, $database->getIdentifier()));
		}
	}
} 