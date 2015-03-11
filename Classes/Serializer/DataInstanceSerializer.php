<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 09.10.14
 * Time: 14:13
 */

namespace Cundd\PersistentObjectStore\Serializer;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;

/**
 * Specialized version of the JsonSerializer to transform JSON data to and from Document instances
 *
 * @package Cundd\PersistentObjectStore\Serializer
 */
class DataInstanceSerializer extends JsonSerializer
{
    /**
     * Serialize the given data
     *
     * @param mixed $data
     * @throws \Cundd\PersistentObjectStore\Serializer\Exception if the data could not be serialized
     * @return string
     */
    public function serialize($data)
    {
        if ($data instanceof DocumentInterface) {
            $objectData                           = $data->getData();
            $objectData[Constants::DATA_META_KEY] = array(
                'guid'             => $data->getGuid(),
                'database'         => $data->getDatabaseIdentifier(),
                'creationTime'     => $data->getCreationTime(),
                'modificationTime' => $data->getModificationTime(),
            );
            return parent::serialize($objectData);
        }
        return parent::serialize($data);
    }

    /**
     * Unserialize the given data
     *
     * @param string $string
     * @throws \Cundd\PersistentObjectStore\Serializer\Exception if the data could not be unserialized
     * @return mixed
     */
    public function unserialize($string)
    {
        $data = parent::unserialize($string);
        if ($data === null) {
            return null;
        }

        $databaseIdentifier = ObjectUtility::valueForKeyPathOfObject(Constants::DATA_META_KEY . '.' . Constants::DATA_DATABASE_KEY,
            $data, '');
        if ($databaseIdentifier) {
            GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
        }
        return new Document($data, $databaseIdentifier);
    }
}
