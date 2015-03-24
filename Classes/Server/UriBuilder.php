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
use Cundd\PersistentObjectStore\Server\Exception\InvalidRequestMethodException;
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
     * @param string                     $actionName   Name of action (e.g. 'list', 'show')
     * @param string                     $actionMethod Method (e.g. 'GET', 'POST')
     * @param ControllerInterface|string $controller   Controller instance or name
     * @param DatabaseInterface|string   $database     Database instance or identifier
     * @param DocumentInterface|string   $document     Document instance or identifier
     * @return string
     */
    public function buildUriFor($actionName, $actionMethod, $controller, $database = null, $document = null)
    {
        if (!$actionName) {
            throw new InvalidUriBuilderArgumentException('Action name must not be empty', 1422475362);
        }
        if (!$actionMethod) {
            throw new InvalidUriBuilderArgumentException('Action method must not be empty', 1422475365);
        }
        if (!$controller) {
            throw new InvalidUriBuilderArgumentException('Controller must not be empty', 1422475419);
        }
        if (!is_string($actionName) || !is_string($actionMethod)) {
            throw new InvalidUriBuilderArgumentException(
                sprintf('Invalid action argument %s', (!is_string($actionName)) ? '$actionName' : '$actionMethod'),
                1422472522
            );
        }
        try {
            GeneralUtility::assertRequestMethod(strtoupper($actionMethod));
        } catch (InvalidRequestMethodException $exception) {
            throw new InvalidUriBuilderArgumentException(sprintf('Invalid action method ', $actionMethod), 1427228089);
        }

        $actionIdentifier = strtolower($actionMethod) . ucfirst($actionName);
        $uriParts         = [];

        $uriParts[] = $this->getControllerNamespaceForController($controller);
        $uriParts[] = $actionIdentifier;

        if ($database) {
            if ($database instanceof DatabaseInterface) {
                $uriParts[] = $database->getIdentifier();
            } elseif (is_scalar($database)) {
                $uriParts[] = (string)$database;
            } else {
                throw new InvalidUriBuilderArgumentException(
                    sprintf('Invalid database argument %s', GeneralUtility::getType($database)),
                    1422472579
                );
            }
        }
        if ($document) {
            if ($document instanceof DocumentInterface) {
                $uriParts[] = $document->getId();
            } elseif (is_scalar($document)) {
                $uriParts[] = (string)$document;
            } else {
                throw new InvalidUriBuilderArgumentException(
                    sprintf('Invalid document argument %s', GeneralUtility::getType($document)),
                    1422472633
                );
            }
        }

        return '/' . implode('/', $uriParts);
    }

    /**
     * Creates the request namespace for the given controller
     *
     * @param ControllerInterface|string $controller Controller instance or name
     * @return string
     */
    public function getControllerNamespaceForController($controller)
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

        return '_' . implode('-', $uriParts);
    }

}