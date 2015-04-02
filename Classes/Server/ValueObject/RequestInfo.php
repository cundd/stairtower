<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 10.10.14
 * Time: 14:49
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Exception\UndefinedMethodCallException;
use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Server\ContentType;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use React\Http\Request;


/**
 * Object that holds information about a parsed request
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class RequestInfo implements Immutable
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
     * @var Request
     */
    protected $request;

    /**
     * Request body
     *
     * @var mixed
     */
    protected $body;

    /**
     * A special handler action that is implemented in the handler
     *
     * @var string
     */
    protected $specialHandlerAction;

    /**
     * Special controller class name
     *
     * @var string
     */
    protected $controllerClass;

    /**
     * Create a new RequestInfo object
     *
     * @param Request $request
     * @param string  $dataIdentifier
     * @param string  $databaseIdentifier
     * @param string  $method
     * @param string  $specialHandlerAction
     * @param string  $controllerClass
     * @param null    $body
     */
    public function __construct(
        $request,
        $dataIdentifier,
        $databaseIdentifier,
        $method,
        $specialHandlerAction = null,
        $controllerClass = null,
        $body = null
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
        $this->method               = $method;
        $this->dataIdentifier       = $dataIdentifier;
        $this->databaseIdentifier   = $databaseIdentifier;
        $this->specialHandlerAction = $specialHandlerAction ?: null;
        $this->request              = $request;
        $this->controllerClass      = $controllerClass ?: null;
        $this->body                 = $body ?: null;
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
     * Returns the original request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
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
     * @return string
     */
    public function getSpecialHandlerAction()
    {
        return $this->specialHandlerAction;
    }

    /**
     * Returns the special handler action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getSpecialHandlerAction();
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
        $nameOffset   = 0;
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
        $request = $this->getRequest();
        if (!$request instanceof Request) {
            return ContentType::JSON_APPLICATION;
        }
        $headers = $this->getRequest()->getHeaders();
        $accept  = '*/*';
        if (isset($headers['Accept'])) {
            $accept = $headers['Accept'];
        }

        $acceptedTypes = explode(',', $accept);
        $sorting       = array(
            ContentType::JSON_APPLICATION => array_search('application/json', $acceptedTypes),
            ContentType::HTML_TEXT        => array_search('text/html', $acceptedTypes),
            ContentType::XML_TEXT         => array_search('text/xml', $acceptedTypes),
        );

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
     * @return mixed
     */
    public function getCookies()
    {
        if (!$this->cookies) {
            $cookieString  = $this->getHeader('Cookie');
            $this->cookies = $this->prepareCookies($cookieString);
        }

        return $this->cookies;
    }

    /**
     * Returns the cookie value for the given name
     *
     * @param string $name
     * @return mixed
     */
    public function getCookie($name)
    {
        $allCookies = $this->getHeaders();
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
        return $this->request->getPath();
    }

    /**
     * Returns the query
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->request->getQuery();
    }

    /**
     * Returns the HTTP version
     *
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->request->getHttpVersion();
    }

    /**
     * Returns the headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->request->getHeaders();
    }

    /**
     * Returns the original request
     *
     * @return Request
     */
    public function getOriginalRequest()
    {
        return $this->request;
    }


    /**
     * Prepares the given cookies
     *
     * @param array|string $cookies
     * @return array
     */
    protected function prepareCookies($cookies)
    {
        if (!is_array($cookies)) {
            $pairs   = explode('; ', $cookies);
            $cookies = array();
            foreach ($pairs as $pair) {
                list($name, $val) = explode('=', $pair, 2);
                $cookies[trim($name)] = trim(urldecode($val));
            }
        }

        return $cookies;
    }

    function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            throw new UndefinedMethodCallException(sprintf('Method %s is not accessible', $name), 1427730222);
        }
        if (!method_exists($this->request, $name)) {
            throw new UndefinedMethodCallException(
                sprintf('Method %s not implemented in %s', $name, get_class($this)),
                1427730223
            );
        }

        if (count($arguments) > 0) {
            return call_user_func_array(array($this->request, $name), $arguments);
        }

        return $this->request->$name();
    }
}