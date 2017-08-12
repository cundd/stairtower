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
     * @return string if the data could not be serialized
     */
    public function serialize($data): string
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
     * @param string $input
     * @return mixed if the data could not be unserialized
     */
    public function unserialize(string $input)
    {
        $data = parent::unserialize($input);
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