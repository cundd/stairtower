<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Serializer;


interface SerializerInterface
{
    /**
     * Serialize the given data
     *
     * @param mixed $data
     * @throws \Cundd\PersistentObjectStore\Serializer\Exception if the data could not be serialized
     * @return string
     */
    public function serialize($data);

    /**
     * Unserialize the given data
     *
     * @param string $string
     * @throws \Cundd\PersistentObjectStore\Serializer\Exception if the data could not be unserialized
     * @return mixed
     */
    public function unserialize($string);
} 