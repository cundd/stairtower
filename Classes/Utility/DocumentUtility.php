<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Utility;

use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Domain\Model\Exception\InvalidDataException;

/**
 * Utility for common Document features
 */
class DocumentUtility
{
    /**
     * Checks if the Document instance's identifier is set
     *
     * @param DocumentInterface|array $document
     * @return DocumentInterface|array Returns the modified object or array
     */
    public static function assertDocumentIdentifier($document)
    {
        if (is_array($document)) {
            if (!isset($document[Constants::DATA_ID_KEY])) {
                $document[Constants::DATA_ID_KEY] = static::getIdentifierForDocument($document);
            }
        } elseif ($document instanceof DocumentInterface) {
            if (!$document->getId()) {
                $document->setValueForKey(static::getIdentifierForDocument($document), Constants::DATA_ID_KEY);
            }
        } else {
            throw new InvalidDataException(
                sprintf(
                    'Given data instance is not of type object but %s',
                    gettype($document)
                ), 1412859398
            );
        }

        return $document;
    }

    /**
     * Returns a unique ID for the given Document
     *
     * @param DocumentInterface|array $document
     * @return string
     */
    public static function getIdentifierForDocument($document)
    {
        $argumentIsArray = is_array($document);

        // Check if the real ID is set
        if ($argumentIsArray) {
            if (isset($document[Constants::DATA_ID_KEY])) {
                return $document[Constants::DATA_ID_KEY];
            }
        } else {
            $value = $document->getId();
            if ($value) {
                return $value;
            }
        }

        // If no read ID is defined check the most common
        $commonIdentifiers = ['id', 'uid', 'email'];
        foreach ($commonIdentifiers as $identifier) {
            if ($argumentIsArray) {
                if (isset($document[$identifier])) {
                    return GeneralUtility::toString($document[$identifier]);
                }
            } else {
                $value = $document->valueForKey($identifier);
                if ($value) {
                    return GeneralUtility::toString($value);
                }
            }
        }

        return sha1(
            sprintf(
                'stairtower_%s_%s_document_%s',
                Constants::VERSION,
                getmypid(),
                microtime()
            )
        );
    }

    /**
     * Sets the Document data's identifier is set
     *
     * @param array $data
     * @return array Returns the modified array
     */
    public static function setDocumentIdentifierOfData(array $data)
    {
        if (!isset($data[Constants::DATA_ID_KEY])) {
            $data[Constants::DATA_ID_KEY] = static::getIdentifierForDocument($data);
        }

        return $data;
    }
}
