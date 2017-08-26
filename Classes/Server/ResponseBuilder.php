<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server;

use Cundd\Stairtower\DataAccess\Exception\ReaderException;
use Cundd\Stairtower\Domain\Model\Exception\InvalidDatabaseException;
use Cundd\Stairtower\Domain\Model\Exception\InvalidDatabaseIdentifierException;
use Cundd\Stairtower\Domain\Model\Exception\InvalidDataException;
use Cundd\Stairtower\Domain\Model\Exception\InvalidDataIdentifierException;
use Cundd\Stairtower\Exception\SecurityException;
use Cundd\Stairtower\Filter\Exception\InvalidCollectionException;
use Cundd\Stairtower\Filter\Exception\InvalidComparisonException;
use Cundd\Stairtower\Filter\Exception\InvalidOperatorException;
use Cundd\Stairtower\Formatter\FormatterInterface;
use Cundd\Stairtower\Formatter\JsonFormatter;
use Cundd\Stairtower\Server\Controller\ControllerResultInterface;
use Cundd\Stairtower\Server\Exception\InvalidBodyException;
use Cundd\Stairtower\Server\Exception\InvalidEventLoopException;
use Cundd\Stairtower\Server\Exception\InvalidRequestActionException;
use Cundd\Stairtower\Server\Exception\InvalidRequestException;
use Cundd\Stairtower\Server\Exception\InvalidRequestMethodException;
use Cundd\Stairtower\Server\Exception\InvalidRequestParameterException;
use Cundd\Stairtower\Server\Exception\InvalidServerChangeException;
use Cundd\Stairtower\Server\Exception\MissingLengthHeaderException;
use Cundd\Stairtower\Server\Exception\RequestMethodNotImplementedException;
use Cundd\Stairtower\Server\Exception\ServerException;
use Cundd\Stairtower\Server\Handler\HandlerResultInterface;
use Cundd\Stairtower\Server\ValueObject\ExceptionResult;
use Cundd\Stairtower\Server\ValueObject\HandlerResult;
use Cundd\Stairtower\Server\ValueObject\RawResult;
use Cundd\Stairtower\Server\ValueObject\RawResultInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;
use Cundd\Stairtower\Utility\ContentTypeUtility;
use DI\Container;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use React\Http\Response;
use Throwable;

class ResponseBuilder implements ResponseBuilderInterface
{
    /**
     * DI container
     *
     * @var Container
     * @Inject
     */
    protected $diContainer;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Container       $diContainer
     * @param LoggerInterface $logger
     */
    public function __construct(Container $diContainer, LoggerInterface $logger)
    {
        $this->diContainer = $diContainer;
        $this->logger = $logger;
    }

    public function buildResponseForResult(
        ?HandlerResultInterface $result,
        RequestInterface $request
    ): ResponseInterface {

        if ($result === null) {
            $formatter = $this->getFormatterForRequest($request);
            $contentType = ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix())
                . '; charset=utf-8';

            return new Response(204, ['Content-Type' => $contentType], $formatter->format('No content'));
        }

        /** @var ResponseInterface $response */
        $response = new Response($result->getStatusCode());


        if ($result instanceof ExceptionResult) {
            return $this->buildErrorResponse($result->getData(), $request);
        }
        if ($result instanceof RawResultInterface) {
            return $this->addResponseBodyIfNotNull(
                $result,
                $response->withHeader('Content-Type', $result->getContentType())
            );
        }

        if ($result instanceof ControllerResultInterface) {
            foreach ($result->getHeaders() as $name => $value) {
                $response = $response->withHeader($name, $value);
            }

            return $this->addResponseBodyIfNotNull($result, $response);
        }

        $formatter = $this->getFormatterForRequest($request);
        $contentType = ContentTypeUtility::convertSuffixToContentType($formatter->getContentSuffix())
            . '; charset=utf-8';

        $response = $response->withHeader('Content-Type', $contentType);

        $responseData = $result->getData();
        if ($responseData !== null) {
            $response->getBody()->write($formatter->format($responseData));
        }

        return $response;
    }

    public function buildErrorResponse(
        Throwable $error,
        RequestInterface $request
    ): ResponseInterface {
        $this->writeError($error);

        if ($error instanceof SecurityException) {
            return new Response('500', [], null);
        }

        if ($error instanceof InvalidRequestActionException) {
            return $this->buildResponseForResult(
                new RawResult($this->getStatusCodeForException($error), $error->getMessage() . 'Raw'),
                $request
            );
        }

        return $this->buildResponseForResult(
            new HandlerResult($this->getStatusCodeForException($error), $error->getMessage()),
            $request
        );
    }

    public function getFormatterForRequest(RequestInterface $request): FormatterInterface
    {
        if ($request->getContentType() === ContentType::XML_TEXT) {
            throw new \Exception('No XML formatter currently implemented');
        } else {
            $formatter = JsonFormatter::class;
        }

        return $this->diContainer->get($formatter);
    }

    public function getStatusCodeForException($error): int
    {
        if (!$error || !($error instanceof Exception)) {
            return 500;
        }
        switch (get_class($error)) {
            case ReaderException::class:
                return ($error->getCode() === 1408127629 ? 400 : 500);

            case InvalidDatabaseException::class:
                return 400;
            case InvalidDatabaseIdentifierException::class:
                return 400;
            case InvalidDataException::class:
                return 500;
            case InvalidDataIdentifierException::class:
                return 400;

            case InvalidBodyException::class:
                return 400;
            case InvalidEventLoopException::class:
                return 500;
            case InvalidRequestException::class:
                return 400;
            case InvalidRequestMethodException::class:
                return 405;
            case InvalidRequestParameterException::class:
                return 400;
            case InvalidServerChangeException::class:
                return 500;
            case MissingLengthHeaderException::class:
                return 411;
            case RequestMethodNotImplementedException::class:
                return 501;
            case ServerException::class:
                return 500;

            case InvalidCollectionException::class:
                return 500;
            case InvalidComparisonException::class:
                return 500;
            case InvalidOperatorException::class:
                return 500;
            default:
                return 500;
        }
    }

    /**
     * @param HandlerResultInterface $result
     * @param ResponseInterface      $response
     * @return ResponseInterface
     */
    protected function addResponseBodyIfNotNull(
        HandlerResultInterface $result,
        ResponseInterface $response
    ): ResponseInterface {
        $responseData = $result->getData();
        if ($responseData !== null) {
            $response->getBody()->write($responseData);
        }

        return $response;
    }

    /**
     * Outputs the given value for information
     *
     * @param \Throwable $error
     */
    private function writeError(\Throwable $error)
    {
        $message = sprintf('Caught exception #%d: %s', $error->getCode(), $error->getMessage());

        $this->logger->error($message, ['file' => $error->getFile(), 'line' => $error->getLine()]);
    }
}
