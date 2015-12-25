<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 27.01.15
 * Time: 21:07
 */

namespace Cundd\PersistentObjectStore\Server;


use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Server\Controller\ControllerInterface;
use Cundd\PersistentObjectStore\Server\Exception\InvalidUriBuilderArgumentException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Class to build URIs for controller actions
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class UriBuilder implements UriBuilderInterface
{
    /**
     * Build the URI with the given arguments
     *
     * @param string                     $action Name of action (e.g. 'list', 'show')
     * @param ControllerInterface|string $controller Controller instance or name
     * @param DatabaseInterface|string   $database Database instance or identifier
     * @param DocumentInterface|string   $document Document instance or identifier
     * @return string
     */
    public function buildUriFor($action, $controller, $database = null, $document = null)
    {
        if (!$action) {
            throw new InvalidUriBuilderArgumentException('Action name must not be empty', 1422475362);
        }
        if (!$controller) {
            throw new InvalidUriBuilderArgumentException('Controller must not be empty', 1422475419);
        }
        if (!is_string($action)) {
            throw new InvalidUriBuilderArgumentException('Invalid action name argument', 1422472522);
        }
        if (!ctype_alnum($action)) {
            throw new InvalidUriBuilderArgumentException('Action name must be alphanumeric', 1451042474);
        }

        $actionIdentifier = $action;
        $uriParts         = [];

        $uriParts[] = $this->getControllerNamespaceForController($controller);
        $uriParts[] = $actionIdentifier;

        if ($document && !$database) {
            throw new InvalidUriBuilderArgumentException(
                'Argument document requires argument database to be set',
                1422475362
            );
        }
        if ($database) {
            $uriParts[] = $this->getDatabaseUriPart($database);
        }
        if ($document) {
            $uriParts[] = $this->getDocumentUriPart($document);
        }

        return '/'.implode('/', $uriParts);
    }

    /**
     * Creates the request namespace for the given controller
     *
     * @param ControllerInterface|string $controller Controller instance or name
     * @return string
     */
    public function getControllerNamespaceForController($controller)
    {
        $controllerClass = $this->getControllerClass($controller);

        $uriParts = [];
        if (strpos($controllerClass, '\\') !== false) {
            $delimiter = '\\';
        } elseif (strpos($controllerClass, '_') !== false) {
            $controllerClass = str_replace('_', '-', $controllerClass);
            $delimiter       = '-';
        } else {
            return '';
        }

        $controllerSuffix       = '_controller';
        $controllerSuffixLength = strlen($controllerSuffix);

        $controllerNameParts = explode(
            $delimiter,
            (GeneralUtility::camelCaseToUnderscore(str_replace('\\', $delimiter, $controllerClass)))
        );

        foreach ($controllerNameParts as $part) {
            if ($part === 'controller') {
                continue;
            }
            if (substr($part, -$controllerSuffixLength) === $controllerSuffix) {
                $uriParts[] = substr($part, 0, -$controllerSuffixLength);
                break;
            }
            $uriParts[] = $part;

        }

        return '_'.implode(self::CONTROLLER_NAME_SEPARATOR, $uriParts);
    }

    /**
     * Returns the last part of the controller's class name
     *
     * @param ControllerInterface|string $controller Controller instance or name
     * @return string
     */
    public function getControllerNameForController($controller)
    {
        $controllerNamespace = $this->getControllerNamespaceForController($controller);

        return substr(strrchr($controllerNamespace, self::CONTROLLER_NAME_SEPARATOR), 1);
    }

    /**
     * Returns the controller's class name or throw an exception
     *
     * @param ControllerInterface|string $controller Controller instance or name
     * @return string
     */
    protected function getControllerClass($controller)
    {
        if ($controller instanceof ControllerInterface) {
            $controllerClass = get_class($controller);
        } elseif (is_scalar($controller)) {
            $controllerClass = (string)$controller;
        } else {
            throw new InvalidUriBuilderArgumentException(
                sprintf('Invalid controller argument %s', GeneralUtility::getType($controller)),
                1422472649
            );
        }

        if (!$controllerClass) {
            throw new InvalidUriBuilderArgumentException('Could not determine the controller class', 1422472650);
        }

        return $controllerClass;
    }

    /**
     * @param $database
     * @return string
     */
    protected function getDatabaseUriPart($database)
    {
        if ($database instanceof DatabaseInterface) {
            $part = $database->getIdentifier();

            return $part;
        } elseif (is_scalar($database)) {
            $part = (string)$database;

            return $part;
        } else {
            throw new InvalidUriBuilderArgumentException(
                sprintf('Invalid database argument %s', GeneralUtility::getType($database)),
                1422472579
            );
        }
    }

    /**
     * @param $document
     * @return string
     */
    protected function getDocumentUriPart($document)
    {
        if ($document instanceof DocumentInterface) {
            $part = $document->getId();

            return $part;
        } elseif (is_scalar($document)) {
            $part = (string)$document;

            return $part;
        } else {
            throw new InvalidUriBuilderArgumentException(
                sprintf('Invalid document argument %s', GeneralUtility::getType($document)),
                1422472633
            );
        }
    }

}