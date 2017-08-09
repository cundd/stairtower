<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Serializer;


use Cundd\Stairtower\Serializer\JsonSerializer;
use PHPUnit\Framework\TestCase;

class JsonSerializerTest extends TestCase
{
    /**
     * @var JsonSerializer
     */
    protected $fixture;

    public function setUp()
    {
        parent::setUp();
        $this->fixture = new JsonSerializer();
    }

    /**
     * @test
     */
    public function serializationTest()
    {
        $input = null;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = 'A string';
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = 'This is some unicode äü ♞ <= do you see the horse?';
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = 0.999009;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = -0.999009;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = 5;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = PHP_INT_MAX;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = 0;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = -100;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = true;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = false;
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = [];
        $this->assertSame($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = new \stdClass();
        $input->firstName = 'Daniel';
        $input->lastName = 'Corn';
        $this->assertEquals(get_object_vars($input), $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = [1 => 'A', 2 => 'B', 3 => 'C'];
        $this->assertEquals($input, $this->fixture->unserialize($this->fixture->serialize($input)));

        $input = range('A', 9);
        $input = array_rand($input, count($input));
        $this->assertEquals($input, $this->fixture->unserialize($this->fixture->serialize($input)));
    }
}
 