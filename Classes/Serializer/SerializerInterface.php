<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Serializer;


interface SerializerInterface
{
    /**
     * Serialize the given data
     *
     * @param mixed $data
     * @return string if the data could not be serialized
     */
    public function serialize($data): string;

    /**
     * Unserialize the given data
     *
     * @param string $input
     * @return mixed if the data could not be unserialized
     * @internal param string $string
     */
    public function unserialize(string $input);
} 