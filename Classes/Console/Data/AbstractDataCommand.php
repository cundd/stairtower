<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Data;


use Cundd\Stairtower\Console\AbstractCommand;
use Cundd\Stairtower\Console\Exception\CommandException;
use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Domain\Model\Exception\InvalidDataException;
use Cundd\Stairtower\Filter\FilterResultInterface;
use Cundd\Stairtower\Serializer\Exception as SerializerException;
use Cundd\Stairtower\Utility\GeneralUtility;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Abstract command that provides functions to load Document instances from input arguments
 */
class AbstractDataCommand extends AbstractCommand
{
    const ARGUMENT_DOCUMENT_ID = 'identifier';
    const ARGUMENT_DATABASE_ID = 'database';

    /**
     * Simple JSON serializer instance
     *
     * @var \Cundd\Stairtower\Serializer\JsonSerializer
     * @Inject
     */
    protected $jsonSerializer;

    /**
     * Filter Builder instance
     *
     * @var \Cundd\Stairtower\Filter\FilterBuilder
     * @Inject
     */
    protected $filterBuilder;


    /**
     * Returns the Document instances from the database defined in the argument 'database' matching the given query
     *
     * @param InputInterface $input
     * @return FilterResultInterface|DocumentInterface|DatabaseInterface
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
        return $this->coordinator->getDatabase($input->getArgument(self::ARGUMENT_DATABASE_ID));
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
        $objectIdentifier = $input->getArgument(self::ARGUMENT_DOCUMENT_ID);
        GeneralUtility::assertDataIdentifier($objectIdentifier);
        $database = $this->findDatabaseInstanceFromInput($input);
        $document = $database->findByIdentifier($objectIdentifier);
        if (!$document && !$graceful) {
            throw new InvalidDataException(
                sprintf(
                    'Object with ID "%s" not found in database %s',
                    $objectIdentifier,
                    $database->getIdentifier()
                )
            );
        }

        return $document;
    }
}
