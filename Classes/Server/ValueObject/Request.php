<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Exception\UndefinedMethodCallException;
use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Server\ContentType;
use Cundd\PersistentObjectStore\Server\Cookie\Cookie;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use React\Http\Request as BaseRequest;


/**
 * Object that holds information about a parsed request
 */
class Request implements Immutable, RequestInterface
{
    /**
     * Identifier for the database
     *
     * @var string
     */
    protected $databaseIdentifier = '';

    /**
     * Identifier for the Document instance
     *
     * @var string
     */
    protected $dataIdentifier = '';

    /**
     * Current request method
     *
     * @var string
     */
    protected $method;

    /**
     * Original request
     *
     * @var BaseRequest
     */
    protected $originalRequest;

    /**
     * Request body
     *
     * @var mixed
     */
    protected $body;

    /**
     * Cookies sent by the client
     *
     * @var Cookie[]
     */
    protected $cookies;

    /**
     * The controller or special handler action
     *
     * @var string
     */
    protected $action;

    /**
     * Special controller class name
     *
     * @var string
     */
    protected $controllerClass;

    /**
     * Create a new Request object
     *
     * @param BaseRequest $request
     * @param string      $dataIdentifier
     * @param string      $databaseIdentifier
     * @param string      $method
     * @param string      $action
     * @param string      $controllerClass
     * @param null        $body
     * @param array       $cookies
     */
    public function __construct(
        $request,
        $dataIdentifier,
        $databaseIdentifier,
        $method,
        $action = null,
        $controllerClass = null,
        $body = null,
        $cookies = []
    ) {
        if ($method) {
            GeneralUtility::assertRequestMethod($method);
        }
        if ($dataIdentifier) {
            GeneralUtility::assertDataIdentifier($dataIdentifier);
        }
        if ($databaseIdentifier) {
            GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
        }
        $this->method = $method;
        $this->dataIdentifier = $dataIdentifier;
        $this->databaseIdentifier = $databaseIdentifier;
        $this->action = $action ?: null;
        $this->originalRequest = $request;
        $this->controllerClass = $controllerClass ?: null;
        $this->body = $body ?: null;
        $this->cookies = $cookies;
    }

    /**
     * Returns the request body
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the identifier for the Document instance
     *
     * @return string
     */
    public function getDataIdentifier()
    {
        return $this->dataIdentifier;
    }

    /**
     * Return the identifier for the database
     *
     * @return string
     */
    public function getDatabaseIdentifier()
    {
        return $this->databaseIdentifier;
    }

    /**
     * Returns the request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns the special handler action
     *
     * @deprecated
     * @return string
     */
    public function getSpecialHandlerAction()
    {
        return $this->getAction();
    }

    /**
     * Returns the controller or special handler action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Returns the name part of the action
     *
     * @return string
     */
    public function getActionName()
    {
        $action = $this->getAction();
        if (!$action) {
            return null;
        }
        $actionPrefix = substr($action, 0, 3);
        $nameOffset = 0;
        switch ($actionPrefix) {
            case 'get':
            case 'put':
                $nameOffset = 3;
                break;

            case 'del' && substr($action, 0, 6) === 'delete':
                $nameOffset = 6;
                break;

            case 'pos' && substr($action, 0, 4) === 'post':
                $nameOffset = 4;
                break;
        }

        return lcfirst(substr($action, $nameOffset, -6));
    }

    /**
     * Returns the special controller class name
     *
     * @return string
     */
    public function getControllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * Returns if the request is a write request
     *
     * @return bool
     */
    public function isWriteRequest()
    {
        return !$this->isReadRequest();
    }

    /**
     * Returns if the request is a read request
     *
     * @return bool
     */
    public function isReadRequest()
    {
        return $this->method === 'GET' || $this->method === 'HEAD';
    }

    /**
     * Returns the requested content type
     *
     * @return string
     */
    public function getContentType()
    {
        $request = $this->getOriginalRequest();
        if (!$request instanceof BaseRequest) {
            return ContentType::JSON_APPLICATION;
        }
        $headers = $this->getHeaders();
        $accept = '*/*';
        if (isset($headers['Accept'])) {
            $accept = $headers['Accept'];
        }

        $acceptedTypes = explode(',', $accept);
        $sorting = [
            ContentType::JSON_APPLICATION => array_search('application/json', $acceptedTypes),
            ContentType::HTML_TEXT        => array_search('text/html', $acceptedTypes),
            ContentType::XML_TEXT         => array_search('text/xml', $acceptedTypes),
        ];

        if ($sorting[ContentType::JSON_APPLICATION] === false) {
            $sorting[ContentType::JSON_APPLICATION] = 1000;
        }
        if ($sorting[ContentType::HTML_TEXT] === false) {
            $sorting[ContentType::HTML_TEXT] = 1010;
        }
        if ($sorting[ContentType::XML_TEXT] === false) {
            $sorting[ContentType::XML_TEXT] = 1020;
        }
        $sorting = array_flip($sorting);
        ksort($sorting);

        return reset($sorting);
    }

    /**
     * Returns the header value for the given name
     *
     * @param string $name
     * @return mixed
     */
    public function getHeader($name)
    {
        $allHeaders = $this->getHeaders();
        if (isset($allHeaders[$name])) {
            return $allHeaders[$name];
        }

        return null;
    }

    /**
     * Returns the cookies
     *
     * @return Cookie[]
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Returns the cookie value for the given name
     *
     * @param string $name
     * @return Cookie
     */
    public function getCookie($name)
    {
        $allCookies = $this->getCookies();
        if (isset($allCookies[$name])) {
            return $allCookies[$name];
        }

        return null;
    }

    /**
     * Returns the path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->originalRequest->getPath();
    }

    /**
     * Returns the query
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->originalRequest->getQuery();
    }

    /**
     * Returns the HTTP version
     *
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->originalRequest->getHttpVersion();
    }

    /**
     * Returns the headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->originalRequest->getHeaders();
    }

    /**
     * Returns the original request
     *
     * @return BaseRequest
     */
    public function getOriginalRequest()
    {
        return $this->originalRequest;
    }

    /**
     * Add a event listener
     *
     * @param string   $event
     * @param callable $listener
     */
    public function on($event, callable $listener)
    {
        $this->originalRequest->on($event, $listener);
    }

    function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            throw new UndefinedMethodCallException(sprintf('Method %s is not accessible', $name), 1427730222);
        }
        if (!method_exists($this->originalRequest, $name)) {
            throw new UndefinedMethodCallException(
                sprintf('Method %s not implemented in %s', $name, get_class($this)),
                1427730223
            );
        }

        if (count($arguments) > 0) {
            return call_user_func_array([$this->originalRequest, $name], $arguments);
        }

        return $this->originalRequest->$name();
    }
}