<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Formatter;

use Cundd\PersistentObjectStore\ArrayableInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;

/**
 * Abstract formatter
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
     * @return FormatterInterface
     */
    public function setConfiguration($configuration): FormatterInterface
    {
        $this->configuration = $configuration;

        return $this;
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
            $foundData = [];
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
            $foundData = [];
            foreach ($data as $key => $dataObject) {
                if ($dataObject instanceof DocumentInterface) {
                    $foundData[$key] = $dataObject->getData();
                } else {
                    $foundData[$key] = $dataObject;
                }
            }

            return $foundData;
        } elseif (is_scalar($data)) {
            return ['message' => $data];
        }

        return $data;
    }
} 