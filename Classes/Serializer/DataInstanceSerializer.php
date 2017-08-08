<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Serializer;

use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Domain\Model\Document;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Utility\GeneralUtility;
use Cundd\Stairtower\Utility\ObjectUtility;


/**
 * Specialized version of the JsonSerializer to transform JSON data to and from Document instances
 */
class DataInstanceSerializer extends JsonSerializer
{
    /**
     * Serialize the given data
     *
     * @param mixed $data
     * @throws \Cundd\Stairtower\Serializer\Exception if the data could not be serialized
     * @return string
     */
    public function serialize($data)
    {
        if ($data instanceof DocumentInterface) {
            $objectData = $data->getData();
            $objectData[Constants::DATA_META_KEY] = [
                'guid'             => $data->getGuid(),
                'database'         => $data->getDatabaseIdentifier(),
                'creationTime'     => $data->getCreationTime(),
                'modificationTime' => $data->getModificationTime(),
            ];

            return parent::serialize($objectData);
        }

        return parent::serialize($data);
    }

    /**
     * Unserialize the given data
     *
     * @param string $string
     * @throws \Cundd\Stairtower\Serializer\Exception if the data could not be unserialized
     * @return mixed
     */
    public function unserialize($string)
    {
        $data = parent::unserialize($string);
        if ($data === null) {
            return null;
        }

        $databaseIdentifier = ObjectUtility::valueForKeyPathOfObject(
            Constants::DATA_META_KEY . '.' . Constants::DATA_DATABASE_KEY,
            $data,
            ''
        );
        if ($databaseIdentifier) {
            GeneralUtility::assertDatabaseIdentifier($databaseIdentifier);
        }

        return new Document($data, $databaseIdentifier);
    }
} 