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
        if (is_array($data) || $data instanceof \Iterator) {
            $foundData = array();
            foreach ($data as $dataObject) {
                if ($dataObject instanceof DocumentInterface) {
                    $foundData[] = $dataObject->getData();
                } else {
                    $foundData[] = $dataObject;
                }
            }
            return $foundData;
        } elseif (is_scalar($data)) {
            return array('message' => $data);
        }
        return $data;
    }
} 