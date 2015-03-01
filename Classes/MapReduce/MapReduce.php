<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28.02.15
 * Time: 12:15
 */

namespace Cundd\PersistentObjectStore\MapReduce;


use Closure;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\MapReduce\Exception\InvalidCallbackException;
use Cundd\PersistentObjectStore\MapReduce\Exception\InvalidEmitKeyException;
use Cundd\PersistentObjectStore\MapReduce\Exception\InvalidValueException;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Iterator;

/**
 * Class MapReduce
 *
 * @package Cundd\PersistentObjectStore\MapReduce
 */
class MapReduce implements MapReduceInterface
{
    /**
     * Undefined callback type
     */
    const CALLBACK_TYPE_NONE = 0;

    /**
     * Callback is a closure
     */
    const CALLBACK_TYPE_CLOSURE = 1;

    /**
     * Callback is an array
     */
    const CALLBACK_TYPE_SIGNATURE_ARRAY = 2;

    /**
     * Callback is a signature in string format
     */
    const CALLBACK_TYPE_SIGNATURE_STRING = 3;

    /**
     * Map callback
     *
     * @var Closure
     */
    protected $mapCallback;

    /**
     * Reduce callback
     *
     * @var Closure
     */
    protected $reduceCallback;

    /**
     * Dictionary of mapping results
     *
     * @var array
     */
    protected $mapResult = array();

    /**
     * Dictionary of reduce results
     *
     * @var array
     */
    protected $reduceResult;

    /**
     * Dictionary of processed objects
     *
     * @var array
     */
    protected $processedObjects;

    /**
     * Creates a new MapReduce instance with the given callbacks
     *
     * @param Closure $mapCallback
     * @param Closure $reduceCallback
     */
    function __construct($mapCallback, $reduceCallback)
    {
        $this->mapCallback    = $this->prepareCallback($mapCallback);
        $this->reduceCallback = $this->prepareCallback($reduceCallback);
        $this->mapResult = array();
    }


    /**
     * Performs the MapReduce operations on the given collection
     *
     * @param DatabaseInterface|Iterator|object[] $collection A collection of objects
     * @return array
     */
    public function perform($collection)
    {
        $this->performMap($collection);
        $this->performReduce($this->mapResult);
        return $this->reduceResult;
    }

    /**
     * Adds the value for the given key to the results
     *
     * @param string $key
     * @param mixed  $value
     */
    public function emit($key, $value)
    {
        if (!is_string($key)) {
            throw new InvalidEmitKeyException(
                sprintf('Given key is not of type string but %s', GeneralUtility::getType($key)),
                1425132007
            );
        } elseif (!$key) {
            throw new InvalidEmitKeyException('Empty key given', 1425132007);
        }
        if (!isset($this->mapResult[$key])) {
            $this->mapResult[$key] = array($value);
        } else {
            $this->mapResult[$key][] = $value;
        }
    }

    /**
     * Perform the mapping part
     *
     * @param DatabaseInterface|Iterator|array $collection
     */
    protected function performMap($collection)
    {
        $mapCallbackLocal      = $this->mapCallback;
        $fixedCollection       = GeneralUtility::collectionToFixedArray($collection, false, false);
        $expectDocuments       = $collection instanceof DatabaseInterface;
        $collectionCount       = $fixedCollection->getSize();
        $processedObjectsLocal = array();
        $i                     = 0;
        while ($i < $collectionCount) {
            $item = $fixedCollection[$i];

            if ($expectDocuments) {
                /** @var DocumentInterface $item */
                $itemIdentifier = $item->getId();
            } elseif (is_object($item)) {
                $itemIdentifier = spl_object_hash($item);
            } else {
                throw new InvalidValueException(
                    sprintf('Given subject is not of an object but of type %s', GeneralUtility::getType($item)),
                    1425139159
                );
            }

            if ($this->needToInvokeMapForIdentifier($itemIdentifier)) {
                $mapCallbackLocal($item);
                if ($expectDocuments) {
                    /** @var DocumentInterface $item */
                    $processedObjectsLocal[$itemIdentifier] = true;
                } elseif (is_object($item)) {
                    $processedObjectsLocal[$itemIdentifier] = true;
                }
            }
            $i++;
        }
        $this->processedObjects = $processedObjectsLocal;
    }

    /**
     * Perform the reduce part
     *
     * @param array $mapResult
     */
    protected function performReduce($mapResult)
    {
        $resultCallbackLocal = $this->reduceCallback;
        $reduceResultLocal   = array();
        $i                   = 0;
        foreach ($mapResult as $key => $value) {
            $reduceResultLocal[$key] = $resultCallbackLocal($key, $value);
            $i++;
        }
        $this->reduceResult = $reduceResultLocal;
    }

    /**
     * Returns if the mapping function has to be invoked for the given item identifier
     *
     * @param string $identifier
     * @return bool
     */
    public function needToInvokeMapForIdentifier($identifier)
    {
        return !isset($this->processedObjects[$identifier]);
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