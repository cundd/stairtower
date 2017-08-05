<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\BodyParser;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Interface for classes that can parse a given request body
 */
interface BodyParserInterface
{
    /**
     * @param string           $data
     * @param RequestInterface $request
     * @return mixed
     */
    public function parse(string $data, RequestInterface $request);
} 