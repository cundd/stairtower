<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 17:11
 */

namespace Cundd\PersistentObjectStore\Server\BodyParser;


use Cundd\PersistentObjectStore\Serializer\Exception;
use Cundd\PersistentObjectStore\Serializer\JsonSerializer;
use Cundd\PersistentObjectStore\Server\Exception\InvalidBodyException;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Body Parser implementation that can parse JSON data
 *
 * @package Cundd\PersistentObjectStore\Server\BodyParser
 */
class JsonBodyParser implements BodyParserInterface
{
    /**
     * @var JsonSerializer
     * @Inject
     */
    protected $serializer;

    /**
     * @param string  $data
     * @param RequestInterface $request
     * @return mixed
     */
    public function parse($data, RequestInterface $request)
    {
        try {
            return $this->serializer->unserialize($data);
        } catch (Exception $exception) {
            throw new InvalidBodyException(sprintf('Could not parse body of request with path %s and method %s',
                $request->getPath(), $request->getMethod()), 1413214227, $exception);
        }
    }

} 