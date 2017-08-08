<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Formatter;

use Cundd\Stairtower\Domain\Model\DocumentInterface;

/**
 * Interface for data formatter responsible to transform Document objects into matching string representations
 */
interface FormatterInterface
{
    /**
     * Sets the configuration for the formatter
     *
     * @param $configuration
     * @return FormatterInterface
     */
    public function setConfiguration($configuration): FormatterInterface;

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