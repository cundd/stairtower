<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 13:09
 */

namespace Cundd\PersistentObjectStore\Formatter;

use Cundd\PersistentObjectStore\ArrayableInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;

/**
 * Abstract formatter
 *
 * @package Cundd\PersistentObjectStore\Formatter
 */
abstract class AbstractFormatter implements FormatterInterface
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * Sets the configuration for the formatter
     *
     * @param $configuration
     * @return $this
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Prepares the given input data to be formatted
     *
     * @param mixed $data
     * @return array
     */
    protected function prepareData($data)
    {
        if ($data instanceof ArrayableInterface) {
            $data = $data->toFixedArray();
        }
        if ($data instanceof \SplFixedArray) {
            $dataCount = $data->count();
            $foundData = array();
            for ($i = 0; $i < $dataCount; $i++) {
                $dataObject = $data[$i];
                if ($dataObject instanceof DocumentInterface) {
                    $foundData[$i] = $dataObject->getData();
                } else {
                    $foundData[$i] = $dataObject;
                }
            }
            return $foundData;
        } elseif (is_array($data) || $data instanceof \Iterator) {
            $foundData = array();
            foreach ($data as $key => $dataObject) {
                if ($dataObject instanceof DocumentInterface) {
                    $foundData[$key] = $dataObject->getData();
                } else {
                    $foundData[$key] = $dataObject;
                }
            }
            return $foundData;
        } elseif (is_scalar($data)) {
            return array('message' => $data);
        }
        return $data;
    }
}
