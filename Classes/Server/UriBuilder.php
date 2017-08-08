<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server;


use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Server\Controller\ControllerInterface;
use Cundd\Stairtower\Server\Exception\InvalidUriBuilderArgumentException;
use Cundd\Stairtower\Utility\GeneralUtility;

/**
 * Class to build URIs for controller actions
 */
class UriBuilder implements UriBuilderInterface
{
    private static $charUnreserved = 'a-zA-Z0-9_\-\.~';
    private static $charSubDelims = '!\$&\'\(\)\*\+,;=';

    /**
     * Build the URI with the given arguments
     *
     * @param string                     $action     Name of action (e.g. 'list', 'show')
     * @param ControllerInterface|string $controller Controller instance or name
     * @param DatabaseInterface|string   $database   Database instance or identifier
     * @param DocumentInterface|string   $document   Document instance or identifier
     * @param array                      $query
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
    ) {
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
        $uriParts = [];

        $uriParts[] = $this->getControllerNamespaceForController($controller);
        $uriParts[] = $actionIdentifier;


        if ($document && !$database) {
            if ($document->getDatabaseIdentifier()) {
                $uriParts[] = $this->getDatabaseUriPart($document->getDatabaseIdentifier());
            } else {
                throw new InvalidUriBuilderArgumentException(
                    'Argument document requires argument database to be set',
                    1422475362
                );
            }
        } elseif ($database) {
            $uriParts[] = $this->getDatabaseUriPart($database);
        }
        if ($document) {
            $uriParts[] = $this->getDocumentUriPart($document);
        }

        $uri = '/' . implode('/', $uriParts);
        if (!empty($query)) {
            $uri .= '?' . $this->filterQueryAndFragment(http_build_query($query));
        }

        if ($fragment) {
            $uri .= '#' . $this->filterQueryAndFragment($fragment);
        }

        return $uri;
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
            $delimiter = '-';
        } else {
            return '';
        }

        $controllerSuffix = '_controller';
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

        return '_' . implode(self::CONTROLLER_NAME_SEPARATOR, $uriParts);
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

    /**
     * Filters the query string or fragment of a URI
     *
     * Taken from https://github.com/guzzle/psr7
     *
     * @param string $input
     *
     * @return string
     */
    private function filterQueryAndFragment(string $input)
    {
        return preg_replace_callback(
            '/(?:[^' . self::$charUnreserved . self::$charSubDelims . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            [$this, 'rawUrlEncodeMatchZero'],
            $input
        );
    }

    private function rawUrlEncodeMatchZero(array $match)
    {
        return rawurlencode($match[0]);
    }
}
