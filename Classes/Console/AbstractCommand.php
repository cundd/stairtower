<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Console;


use Symfony\Component\Console\Command\Command;


/**
 * Abstract command
 */
abstract class AbstractCommand extends Command
{
    /**
     * Document Access Coordinator
     *
     * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    /**
     * Serializer instance
     *
     * @var \Cundd\PersistentObjectStore\Serializer\DataInstanceSerializer
     * @Inject
     */
    protected $serializer;

    /**
     * Formatter
     *
     * @var \Cundd\PersistentObjectStore\Formatter\JsonFormatter
     * @Inject
     */
    protected $formatter;

}