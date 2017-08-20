<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console;


use Symfony\Component\Console\Command\Command;


/**
 * Abstract command
 */
abstract class AbstractCommand extends Command
{
    /**
     * Document Access Coordinator
     *
     * @var \Cundd\Stairtower\DataAccess\CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    /**
     * Serializer instance
     *
     * @var \Cundd\Stairtower\Serializer\DataInstanceSerializer
     * @Inject
     */
    protected $serializer;

    /**
     * Formatter
     *
     * @var \Cundd\Stairtower\Formatter\JsonFormatter
     * @Inject
     */
    protected $formatter;
}
