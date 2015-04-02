<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 11:47
 */

namespace Cundd\PersistentObjectStore\Formatter;

use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;

/**
 * Interface for data formatter responsible to transform Document objects into matching string representations
 *
 * @package Cundd\PersistentObjectStore\Formatter
 */
interface FormatterInterface
{
    /**
     * Sets the configuration for the formatter
     *
     * @param $configuration
     * @return $this
     */
    public function setConfiguration($configuration);

    /**
     * Formats the given input
     *
     * @param DocumentInterface|DocumentInterface[]|\SplFixedArray|string $input
     * @return string
     */
    public function format($input);

    /**
     * Returns the content suffix for the formatter
     *
     * @return string
     */
    public function getContentSuffix();
}