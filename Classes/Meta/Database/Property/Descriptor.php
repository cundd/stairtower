<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Meta\Database\Property;

use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\DatabaseRawDataInterface;
use Cundd\Stairtower\Meta\DescriptorInterface;
use Cundd\Stairtower\Meta\Exception\DescriptorSubjectException;
use Cundd\Stairtower\Utility\GeneralUtility;

/**
 * Descriptor for properties in a Database
 *
 * @package Cundd\Stairtower\Meta\Database
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
                DatabaseInterface::class, $subject, 1424896728
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
