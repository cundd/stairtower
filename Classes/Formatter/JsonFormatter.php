<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Formatter;

use Cundd\Stairtower\Domain\Model\DocumentInterface;


/**
 * Class JsonFormatter
 */
class JsonFormatter extends AbstractFormatter
{
    /**
     * @var \Cundd\Stairtower\Serializer\DataInstanceSerializer
     * @Inject
     */
    protected $serializer;

    /**
     * Formats the given input
     *
     * @param DocumentInterface|DocumentInterface[]|\SplFixedArray|string $input
     * @return string
     */
    public function format($input)
    {
        return $this->serializer->serialize($this->prepareData($input));
    }

    /**
     * Returns the content suffix for the formatter
     *
     * @return string
     */
    public function getContentSuffix()
    {
        return 'json';
    }

} 