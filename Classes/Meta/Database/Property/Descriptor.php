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
 */
class Descriptor implements DescriptorInterface
{
    public function describe($subject): array
    {
        if (!$subject instanceof DatabaseRawDataInterface) {
            throw DescriptorSubjectException::descriptorException(
                DatabaseInterface::class,
                $subject,
                1424896728
            );
        }

        $fixedDataCollection = $subject->getRawData();
        $dataCollectionCount = $fixedDataCollection->getSize();
        $propertyMap = [];
        $i = 0;
        while ($i < $dataCollectionCount) {
            $documentData = $fixedDataCollection[$i];
            if (is_array($documentData)) {
                //$propertyMap = array_merge($propertyMap, array_flip(array_keys($documentData)));
                //$propertyMap = array_merge($propertyMap, array_keys($documentData));


                //while(list($propertyKey, $value) = each($documentData)) {
                //    if (!isset($propertyMap[$propertyKey])) {
                //        $propertyMap[$propertyKey] = GeneralUtility::getType($value);
                //    }
                //}

                foreach ($documentData as $propertyKey => $value) {
                    $type = GeneralUtility::getType($value);
                    if (!isset($propertyMap[$propertyKey])) {
                        $propertyMap[$propertyKey] = [$type => 1];
                    } else {
                        $propertyMap[$propertyKey][$type] += 1;
                    }
                }
            }
            $i += 1;
        }

        $descriptionCollection = [];
        foreach ($propertyMap as $propertyKey => $types) {
            $descriptionCollection[$propertyKey] = new Description($propertyKey, array_keys($types), array_sum($types));
        }

        return $descriptionCollection;
    }
}
