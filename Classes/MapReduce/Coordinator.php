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
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Iterator;

class Coordinator implements CoordinatorInterface
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

    function __construct($mapCallback, $reduceCallback)
    {
        $this->mapCallback    = $this->prepareCallback($mapCallback);
        $this->reduceCallback = $this->prepareCallback($reduceCallback);
    }


    /**
     * Performs the operations on the given collection
     *
     * @param DatabaseInterface|Iterator|array $collection
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
        $mapCallbackLocal = $this->mapCallback;
        $fixedCollection  = GeneralUtility::collectionToFixedArray($collection, false, false);
        $collectionCount  = $fixedCollection->getSize();
        $i                = 0;
        while ($i < $collectionCount) {
            /** @var DocumentInterface $item */
            $item = $fixedCollection[$i];
            $mapCallbackLocal($item);
            $i++;
        }
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


    ///**
    // * Invoke the given callback
    // *
    // * @param DatabaseInterface|Iterator|array $collection
    // * @param Closure $callback
    // * @return array
    // */
    //protected function performCallback($collection, $callback)
    //{
    //    $this->assertCallback($callback);
    //    $fixedCollection = GeneralUtility::collectionToFixedArray($collection, false, false);
    //    $resu  = array();
    //    $collectionCount = $fixedCollection->getSize();
    //    $i               = 0;
    //    while ($i < $collectionCount) {
    //        /** @var DocumentInterface $item */
    //        $item = $fixedCollection[$i];
    //        $resultId = $item->getId();
    //
    //        $mapResultLocal[$resultId] = call_user_func($callback, $item);
    //        $i++;
    //    }
    //
    //    $this->mapResult = $mapResultLocal;
    //    return $mapResultLocal;
    //}

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

    ///**
    // * Validates the given callback
    // *
    // * @param Closure $callback
    // * @return int
    // */
    //protected function assertCallback($callback){
    //    if (!is_callable($callback)) {
    //        throw new InvalidCallbackException('Given argument is not callable', 1425127889);
    //    }
    //    if ($callback instanceof Closure) {
    //        return self::CALLBACK_TYPE_CLOSURE;
    //    } elseif (is_string($callback)) {
    //        return self::CALLBACK_TYPE_SIGNATURE_STRING;
    //    } elseif (is_array($callback)) {
    //        return self::CALLBACK_TYPE_SIGNATURE_ARRAY;
    //    }
    //    throw new InvalidCallbackException('Could not detect callback type of argument', 1425127890);
    //}
}