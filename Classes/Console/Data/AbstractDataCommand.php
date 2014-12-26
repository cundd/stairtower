<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 09.10.14
 * Time: 16:09
 */

namespace Cundd\PersistentObjectStore\Console\Data;


use Cundd\PersistentObjectStore\Console\AbstractCommand;
use Cundd\PersistentObjectStore\Console\Exception\CommandException;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDataException;
use Cundd\PersistentObjectStore\Filter\FilterResultInterface;
use Cundd\PersistentObjectStore\Serializer\Exception as SerializerException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Abstract command that provides functions to load Document instances from input arguments
 *
 * @package Cundd\PersistentObjectStore\Console\Document
 */
class AbstractDataCommand extends AbstractCommand
{
    /**
     * Simple JSON serializer instance
     *
     * @var \Cundd\PersistentObjectStore\Serializer\JsonSerializer
     * @Inject
     */
    protected $jsonSerializer;

    /**
     * Filter Builder instance
     *
     * @var \Cundd\PersistentObjectStore\Filter\FilterBuilder
     * @Inject
     */
    protected $filterBuilder;


    /**
     * Returns the Document instances from the database defined in the argument 'database' matching the given query
     *
     * @param InputInterface $input
     * @return FilterResultInterface
     */
    protected function filterDataInstanceFromInput(InputInterface $input)
    {
        try {
            $query = $this->jsonSerializer->unserialize($input->getArgument('query'));
        } catch (SerializerException $exception) {
            throw new CommandException('Could not parse given query', 1419439407, $exception);
        }
        $database = $this->findDatabaseInstanceFromInput($input);
        if (is_string($query)) {
            GeneralUtility::assertDataIdentifier($query);
            return $database->findByIdentifier($query);
        } elseif (!empty($query)) {
            $filter = $this->filterBuilder->buildFilter($query);
            return $filter->filterCollection($database);
        }
        return $database;
    }

    /**
     * Returns the Database instance defined by the arguments 'database'
     *
     * @param InputInterface $input
     * @return DatabaseInterface
     */
    protected function findDatabaseInstanceFromInput(InputInterface $input)
    {
        $databaseIdentifier = $input->getArgument('database');
        return $this->coordinator->getDatabase($databaseIdentifier);
    }

    /**
     * Returns the Document instance defined by the arguments 'database' and 'identifier' and will throw an exception if
     * none is found and graceful is FALSE
     *
     * @param InputInterface $input
     * @param bool           $graceful
     * @return DocumentInterface
     */
    protected function findDataInstanceFromInput(InputInterface $input, $graceful = false)
    {
        $objectIdentifier = $input->getArgument('identifier');
        GeneralUtility::assertDataIdentifier($objectIdentifier);
        $database = $this->findDatabaseInstanceFromInput($input);
        $document = $database->findByIdentifier($objectIdentifier);
        if (!$document && !$graceful) {
            throw new InvalidDataException(sprintf('Object with ID "%s" not found in database %s', $objectIdentifier,
                $database->getIdentifier()));
        }
        return $document;
    }
} 