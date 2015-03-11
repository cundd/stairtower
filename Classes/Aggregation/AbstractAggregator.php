<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 03.03.15
 * Time: 20:59
 */

namespace Cundd\PersistentObjectStore\Aggregation;

use Closure;
use Cundd\PersistentObjectStore\Aggregation\Exception\InvalidCallbackException;
use Cundd\PersistentObjectStore\Aggregation\Exception\InvalidValueException;
use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Iterator;


/**
 * Abstract aggregator
 *
 * @package Cundd\PersistentObjectStore\Aggregation
 */
abstract class AbstractAggregator implements AggregatorInterface
{
    /**
     * Map callback
     *
     * @var Closure
     */
    protected $aggregationCallback;

    /**
     * Dictionary of processed objects
     *
     * @var array
     */
    protected $processedObjects;

    /**
     * Returns if the aggregation function has to be invoked for the given item
     *
     * @param object $item
     * @return bool
     */
    public function needToPerformAggregationForItem($item)
    {
        $itemIdentifier = $this->getItemIdentifier($item);
        return !isset($this->processedObjects[$itemIdentifier]);
    }

    /**
     * Determines the item's identifier
     *
     * @param object $item
     * @return string
     */
    protected function getItemIdentifier($item)
    {
        if (is_object($item)) {
            if ($item instanceof DocumentInterface) {
                $itemIdentifier = $item->getId();
                return $itemIdentifier;
            } else {
                $itemIdentifier = spl_object_hash($item);
                return $itemIdentifier;
            }
        } elseif (is_array($item) && isset($item[Constants::DATA_ID_KEY])) {
            $itemIdentifier = $item[Constants::DATA_ID_KEY];
            return $itemIdentifier;
        } else {
            throw new InvalidValueException(
                sprintf('Given subject is not of an object but of type %s', GeneralUtility::getType($item)),
                1425139159
            );
        }
    }

    /**
     * Invokes the aggregation callback with each of the collection's objects
     *
     * @param DatabaseInterface|Iterator|array $collection
     */
    protected function performAggregation($collection)
    {
        $aggregationCallbackLocal = $this->aggregationCallback;
        $fixedCollection          = GeneralUtility::collectionToFixedArray($collection, false, false);
        $expectDocuments          = $collection instanceof DatabaseInterface;
        $collectionCount          = $fixedCollection->getSize();
        $processedObjectsLocal    = array();
        $i                        = 0;
        while ($i < $collectionCount) {
            $item = $fixedCollection[$i];
            if ($this->needToPerformAggregationForItem($item)) {
                $aggregationCallbackLocal($item);
                if ($expectDocuments) {
                    /** @var DocumentInterface $item */
                    $processedObjectsLocal[$item->getId()] = true;
                } else {
                    $processedObjectsLocal[$this->getItemIdentifier($item)] = true;
                }
            }
            $i++;
        }
        $this->processedObjects = $processedObjectsLocal;
    }

    /**
     * Validates the given callback
     *
     * @param Closure $callback
     */
    protected function prepareCallback($callback)
    {
        if ($callback instanceof Closure) {
            return Closure::bind($callback, $this, __CLASS__);
        }
        throw new InvalidCallbackException('Given argument is not of type closure', 1425127889);
    }
}