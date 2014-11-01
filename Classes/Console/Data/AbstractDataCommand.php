<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 09.10.14
 * Time: 16:09
 */

namespace Cundd\PersistentObjectStore\Console\Data;


use Cundd\PersistentObjectStore\Console\AbstractCommand;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DataInterface;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDataException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Abstract command that provides functions to load Document instances from input arguments
 *
 * @package Cundd\PersistentObjectStore\Console\Document
 */
class AbstractDataCommand extends AbstractCommand {
	/**
	 * Returns the Database instance defined by the arguments 'database'
	 *
	 * @param InputInterface $input
	 * @return DatabaseInterface
	 */
	protected function findDatabaseInstanceFromInput(InputInterface $input) {
		$databaseIdentifier = $input->getArgument('database');
		return $this->coordinator->getDatabase($databaseIdentifier);
	}

	/**
	 * Returns the Document instance defined by the arguments 'database' and 'identifier' and will throw an exception if
	 * none is found and graceful is FALSE
	 *
	 * @param InputInterface $input
	 * @param bool           $graceful
	 * @return DataInterface
	 */
	protected function findDataInstanceFromInput(InputInterface $input, $graceful = FALSE) {
		$objectIdentifier = $input->getArgument('identifier');
		GeneralUtility::assertDataIdentifier($objectIdentifier);
		$database = $this->findDatabaseInstanceFromInput($input);
		$dataInstance = $database->findByIdentifier($objectIdentifier);
		if (!$dataInstance && !$graceful) throw new InvalidDataException(sprintf('Object with ID "%s" not found in database %s', $objectIdentifier, $database->getIdentifier()));
		return $dataInstance;
	}
} 