<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;

use Cundd\Stairtower\Exception\UndefinedMethodCallException;
use Cundd\Stairtower\Immutable;
use Cundd\Stairtower\Server\ContentType;
use Cundd\Stairtower\Server\Cookie\Cookie;
use Cundd\Stairtower\Utility\DebugUtility;
use Cundd\Stairtower\Utility\GeneralUtility;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

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
    private $databaseIdentifier = '';

    /**
     * Identifier for the Document instance
     *
     * @var string
     */
    private $dataIdentifier = '';

    /**
     * Current request method
     *
     * @var string
     */
    private $method;

    /**
     * Original request
     *
     * @var ServerRequestInterface
     */
    private $originalRequest;

    /**
     * Request body
     *
     * @var StreamInterface|null
     */
    private $body;

    /**
     * The controller or special handler action
     *
     * @var string
     */
    private $action;

    /**
     * Special controller class name
     *
     * @var string
     */
    private $controllerClass;

    /**
     * Parsed body
     *
     * @var array|string
     */
    private $parsedBody;

    /**
     * Create a new Request object
     *
     * @param ServerRequestInterface $request
     * @param string                 $dataIdentifier
     * @param string                 $databaseIdentifier
     * @param string                 $method
     * @param string                 $action
     * @param string                 $controllerClass
     * @param StreamInterface|null   $body
     * @param null|array|object      $parsedBody
     */
    public function __construct(
        $request,
        string $dataIdentifier,
        string $databaseIdentifier,
        string $method,
        string $action = '',
        string $controllerClass = '',
        ?StreamInterface $body = null,
        $parsedBody = null
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
        $this->body = $body;
        $this->method = $method;
        $this->action = $action;
        $this->parsedBody = $parsedBody;
        $this->dataIdentifier = $dataIdentifier;
        $this->originalRequest = $request;
        $this->controllerClass = $controllerClass;
        $this->databaseIdentifier = $databaseIdentifier;
    }

    public function getBody(): ?StreamInterface
    {
        return $this->body;
    }

    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function withParsedBody($data): RequestInterface
    {
        if (null !== $data && !is_array($data) && !is_object($data)) {
            throw new \InvalidArgumentException(
                sprintf('Data must be either null, array or object, %s given', gettype($data))
            );
        }

        if ($data == ["php://input" => ""]) {
            DebugUtility::var_dump($data);
            echo '<pre>';
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            echo '</pre>';
            throw new \InvalidArgumentException('Data does not make sense');
        }

        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    public function getDataIdentifier(): string
    {
        return $this->dataIdentifier;
    }

    public function getDatabaseIdentifier(): string
    {
        return $this->databaseIdentifier;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getActionName(): string
    {
        $action = $this->getAction();
        if (!$action) {
            return '';
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

    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    public function isWriteRequest(): bool
    {
        return !$this->isReadRequest();
    }

    public function isReadRequest(): bool
    {
        return $this->method === 'GET' || $this->method === 'HEAD';
    }

    public function getContentType(): string
    {
        $request = $this->getOriginalRequest();
        if (!$request instanceof ServerRequestInterface) {
            return ContentType::JSON_APPLICATION;
        }
        $acceptValues = $request->getHeader('Accept');
        $accept = reset($acceptValues);
        if (!$accept) {
            return ContentType::JSON_APPLICATION;
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

        return (string)reset($sorting);
    }

    public function getHeader(string $name): array
    {
        return $this->originalRequest->getHeader($name);
    }

    public function getCookies(): array
    {
        $parsedCookies = $this->originalRequest->getCookieParams();
        $cookieObjects = [];
        foreach ($parsedCookies as $cookieName => $cookieValue) {
            $cookieObjects[$cookieName] = new Cookie(
                $cookieName,
                $cookieValue,
                $parsedCookies['expires'],
                (string)$parsedCookies['path'],
                (string)$parsedCookies['domain'],
                (bool)$parsedCookies['secure'],
                (bool)($parsedCookies['http_only'] ?? false)
            );
        }

        return $cookieObjects;
    }

    public function getCookie(string $name):?Cookie
    {
        $allCookies = $this->getCookies();
        if (isset($allCookies[$name])) {
            return $allCookies[$name];
        }

        return null;
    }

    public function getPath(): string
    {
        return $this->originalRequest->getUri()->getPath();
    }

    public function getUri(): UriInterface
    {
        return $this->originalRequest->getUri();
    }

    public function getQuery(): array
    {
        return $this->originalRequest->getQueryParams();
    }

    public function getHttpVersion(): string
    {
        return $this->originalRequest->getProtocolVersion();
    }

    public function getHeaders()
    {
        return $this->originalRequest->getHeaders();
    }

    /**
     * Returns the original request
     *
     * @return ServerRequestInterface
     */
    public function getOriginalRequest(): ServerRequestInterface
    {
        return $this->originalRequest;
    }

    function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            throw new UndefinedMethodCallException(
                sprintf('Method %s is not accessible', $name),
                1427730222
            );
        }
        if (!method_exists($this->originalRequest, $name)) {
            throw new UndefinedMethodCallException(
                sprintf('Method %s not implemented in %s', $name, get_class($this)),
                1427730223
            );
        }

        return $this->originalRequest->$name(...$arguments);
    }
}
