<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Serializer;


interface SerializerInterface
{
    /**
     * Serialize the given data
     *
     * @param mixed $data
     * @throws \Cundd\Stairtower\Serializer\Exception if the data could not be serialized
     * @return string
     */
    public function serialize($data);

    /**
     * Unserialize the given data
     *
     * @param string $string
     * @throws \Cundd\Stairtower\Serializer\Exception if the data could not be unserialized
     * @return mixed
     */
    public function unserialize($string);
} 