<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.02.15
 * Time: 21:08
 */

namespace Cundd\PersistentObjectStore\Meta\Database\Property;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseRawDataInterface;
use Cundd\PersistentObjectStore\Meta\DescriptorInterface;
use Cundd\PersistentObjectStore\Meta\Exception\DescriptorSubjectException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Descriptor for properties in a Database
 *
 * @package Cundd\PersistentObjectStore\Meta\Database
 */
class Descriptor implements DescriptorInterface
{
    /**
     * Returns the description of the subject
     *
     * @param DatabaseInterface $subject
     * @return mixed
     */
    public function describe($subject)
    {
        if (!$subject instanceof DatabaseRawDataInterface) {
            throw DescriptorSubjectException::descriptorException(
                'Cundd\\PersistentObjectStore\\Domain\\Model\\DatabaseInterface', $subject, 1424896728
            );
        }

        $fixedDataCollection = $subject->getRawData();
        $dataCollectionCount = $fixedDataCollection->getSize();
        $propertyMap         = array();
        $i                   = 0;
        while ($i < $dataCollectionCount) {
            $item = $fixedDataCollection[$i];
            if (is_array($item)) {
                //$propertyMap = array_merge($propertyMap, array_flip(array_keys($item)));
                //$propertyMap = array_merge($propertyMap, array_keys($item));


                //while(list($property, $value) = each($item)) {
                //    if (!isset($propertyMap[$property])) {
                //        $propertyMap[$property] = GeneralUtility::getType($value);
                //    }
                //}

                foreach ($item as $property => $value) {
                    if (!isset($propertyMap[$property])) {
                        $propertyMap[$property] = [
                            GeneralUtility::getType($value) => 1
                        ];
                    }
                    $propertyMap[$property][GeneralUtility::getType($value)]++;
                }
            }
            $i++;
        }

        $descriptionCollection = array();
        foreach ($propertyMap as $propertyKey => $types) {
            $descriptionCollection[] = new Description($propertyKey, array_keys($types), array_sum($types));
        }
        return $descriptionCollection;
    }
}
