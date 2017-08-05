<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\BodyParser;

use Cundd\PersistentObjectStore\Serializer\Exception;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;
use Cundd\PersistentObjectStore\Server\Exception\InvalidBodyException;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Body Parser implementation that can parse JSON data
 */
class JsonBodyParser implements BodyParserInterface
{
    /**
     * @var JsonSerializer
     * @Inject
     */
    protected $serializer;

    /**
     * @param string           $data
     * @param RequestInterface $request
     * @return mixed
     */
    public function parse(string $data, RequestInterface $request)
    {
        try {
            return $this->serializer->unserialize($data);
        } catch (Exception $exception) {
            throw new InvalidBodyException(
                sprintf(
                    'Could not parse body of request with path %s and method %s',
                    $request->getPath(),
                    $request->getMethod()
                ), 1413214227, $exception
            );
        }
    }

} 