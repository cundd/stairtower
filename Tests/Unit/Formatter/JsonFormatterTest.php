<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Formatter;

use Cundd\Stairtower\Domain\Model\Document;
use Cundd\Stairtower\Formatter\JsonFormatter;
use Cundd\Stairtower\Tests\Unit\AbstractCase;

/**
 * Test class for JsonFormatter
 */
class JsonFormatterTest extends AbstractCase
{
    /**
     * @var JsonFormatter
     */
    protected $fixture;

    /**
     * @test
     */
    public function formatTest()
    {
        $expected = '{
    "message": "a message"
}';
        $this->assertEquals($expected, $this->fixture->format('a message'));

        $expected = '{
    "a key": "a value"
}';
        $this->assertEquals($expected, $this->fixture->format(['a key' => 'a value']));

        $expected = '{
    "a key": "a value",
    "__meta": {
        "guid": "-",
        "database": null,
        "creationTime": null,
        "modificationTime": null
    }
}';
        $this->assertEquals($expected, $this->fixture->format(new Document(['a key' => 'a value'])));

        $expected = '[
    {
        "a key": "a value"
    },
    {
        "second key": "second value"
    }
]';
        $this->assertEquals(
            $expected,
            $this->fixture->format(
                [
                    new Document(['a key' => 'a value']),
                    new Document(['second key' => 'second value']),
                ]
            )
        );
    }

    /**
     * @test
     */
    public function getContentSuffixTest()
    {
        $this->assertEquals('json', $this->fixture->getContentSuffix());
    }
}
