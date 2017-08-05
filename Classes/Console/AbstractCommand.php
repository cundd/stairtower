<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console;


use Symfony\Component\Console\Command\Command;


/**
 * Abstract command
 *
 * @package Cundd\PersistentObjectStore\Console
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