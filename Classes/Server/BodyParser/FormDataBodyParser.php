<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\BodyParser;

use Cundd\PersistentObjectStore\Server\ValueObject\RequestInterface;

/**
 * Body Parser implementation that can parse form data
 */
class FormDataBodyParser implements BodyParserInterface
{
    /**
     * @param string           $data
     * @param RequestInterface $request
     * @return mixed
     */
    public function parse(string $data, RequestInterface $request)
    {
        $parsedData = [];
        parse_str($data, $parsedData);

        return $parsedData;
    }

} 