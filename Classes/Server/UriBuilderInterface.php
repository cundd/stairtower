<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 27.01.15
 * Time: 20:58
 */

namespace Cundd\PersistentObjectStore\Server;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Server\Controller\ControllerInterface;

/**
 * Interface for UriBuilder instances
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
interface UriBuilderInterface
{
    /**
     * String to separate controller class name parts in an URL
     */
    const CONTROLLER_NAME_SEPARATOR = '-';

    /**
     * Build the URI with the given arguments
     *
     * @param string                     $action     Name of action (e.g. 'list', 'show')
     * @param ControllerInterface|string $controller Controller instance or name
     * @param DatabaseInterface|string   $database   Database instance or identifier
     * @param DocumentInterface|string   $document   Document instance or identifier
     * @param string[]                   $query      Additional GET query parameters
     * @return string
     */
    public function buildUriFor($action, $controller, $database = null, $document = null, array $query = []);

    /**
     * Creates the request namespace for the given controller
     *
     * @param ControllerInterface|string $controller Controller instance or name
     * @return string
     */
    public function getControllerNamespaceForController($controller);
}