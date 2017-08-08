<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server;

use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Server\Controller\ControllerInterface;

/**
 * Interface for UriBuilder instances
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
     * @param string                     $fragment
     * @return string
     */
    public function buildUriFor(
        $action,
        $controller,
        $database = null,
        $document = null,
        array $query = [],
        string $fragment = ''
    );

    /**
     * Creates the request namespace for the given controller
     *
     * @param ControllerInterface|string $controller Controller instance or name
     * @return string
     */
    public function getControllerNamespaceForController($controller);
}