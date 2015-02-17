<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 27.01.15
 * Time: 20:58
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

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
     * Build the URI with the given arguments
     *
     * @param string                     $actionName
     * @param string                     $actionMethod
     * @param ControllerInterface|string $controller Controller instance or name
     * @param DatabaseInterface|string   $database   Database instance or identifier
     * @param DocumentInterface|string   $document   Document instance or identifier
     * @return string
     */
    public function buildUriFor($actionName, $actionMethod, $controller, $database = null, $document = null);

    /**
     * Creates the request namespace for the given controller
     *
     * @param ControllerInterface|string $controller Controller instance or name
     * @return string
     */
    public function getControllerNamespaceForController($controller);
}