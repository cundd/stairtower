<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 07.01.15
 * Time: 21:16
 */

namespace Cundd\PersistentObjectStore\Formatter;

use Cundd\PersistentObjectStore\AbstractCase;
use Cundd\PersistentObjectStore\Domain\Model\Document;

/**
 * Test class for JsonFormatter
 *
 * @package Cundd\PersistentObjectStore\Formatter
 */
class RawFormatterTest extends AbstractCase
{
    /**
     * @var RawFormatter
     */
    protected $fixture;

    /**
     * @test
     */
    public function formatTest()
    {
        $input = 'a message';
        $this->assertEquals($input, $this->fixture->format($input));

        $input = array('a key' => 'a value');
        $this->assertEquals($input, $this->fixture->format($input));

        $expected = '{}';
        $this->assertEquals($expected, $this->fixture->format(new Document(array('a key' => 'a value'))));

        $expected = '[
    {
        "a key": "a value"
    },
    {
        "second key": "second value"
    }
]';
        $this->assertEquals($expected, $this->fixture->format([
            new Document(array('a key' => 'a value')),
            new Document(array('second key' => 'second value')),
        ]));
    }

    /**
     * @test
     */
    public function getContentSuffixTest()
    {
        $this->assertEquals('', $this->fixture->getContentSuffix());

        $this->fixture->setContentSuffix('html');
        $this->assertEquals('html', $this->fixture->getContentSuffix());

        $this->fixture->setContentSuffix('json');
        $this->assertEquals('json', $this->fixture->getContentSuffix());

        $this->fixture->setContentSuffix('xml');
        $this->assertEquals('xml', $this->fixture->getContentSuffix());
    }
}
