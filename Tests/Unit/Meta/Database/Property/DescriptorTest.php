<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Meta\Database\Property;

use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Domain\Model\DatabaseRawDataInterface;
use Cundd\Stairtower\Tests\Unit\AbstractDatabaseBasedCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Test for Descriptor
 */
class DescriptorTest extends AbstractDatabaseBasedCase
{
    /**
     * @var Descriptor
     */
    protected $fixture;

    protected function setUp()
    {
        parent::setUp();
        $this->fixture = new Descriptor();
    }

    protected function tearDown()
    {
        unset($this->fixture);
        parent::tearDown();
    }

    /**
     * @test
     */
    public function describeDatabaseTest()
    {
        $database = $this->getSmallPeopleDatabase();

        /** @var Description[] $result */
        $result = $this->fixture->describe($database);
        $this->assertInternalType('array', $result);
        $this->assertCount(21, $result);

        $this->assertArrayHasKey('index', $result);
        $this->assertSame($database->count(), $result['index']->getCount());
        $this->assertSame([Description::TYPE_INTEGER], $result['index']->getTypes());

        $this->assertArrayHasKey(Constants::DATA_ID_KEY, $result);
        $this->assertSame($database->count(), $result[Constants::DATA_ID_KEY]->getCount());
        $this->assertSame([Description::TYPE_STRING], $result[Constants::DATA_ID_KEY]->getTypes());

        $this->assertArrayHasKey('name', $result);
        $this->assertSame($database->count(), $result['name']->getCount());
        $this->assertSame([Description::TYPE_STRING], $result['name']->getTypes());

        $this->assertArrayHasKey('age', $result);
        $this->assertSame($database->count(), $result['age']->getCount());
        $this->assertSame([Description::TYPE_INTEGER], $result['age']->getTypes());

        $this->assertArrayHasKey('friends', $result);
        $this->assertSame($database->count(), $result['friends']->getCount());
        $this->assertSame([Description::TYPE_ARRAY], $result['friends']->getTypes());

        $this->assertArrayHasKey('isActive', $result);
        $this->assertSame($database->count(), $result['isActive']->getCount());
        $this->assertSame([Description::TYPE_BOOLEAN], $result['isActive']->getTypes());
    }

    /**
     * @test
     */
    public function describeEmptyDatabaseTest()
    {
        /** @var DatabaseRawDataInterface|ObjectProphecy $databaseProphecy */
        $databaseProphecy = $this->prophesize(DatabaseRawDataInterface::class);
        $databaseProphecy->getRawData()->willReturn(new \SplFixedArray(0));
        /** @var DatabaseRawDataInterface $database */
        $database = $databaseProphecy->reveal();

        /** @var Description[] $result */
        $result = $this->fixture->describe($database);
        $this->assertInternalType('array', $result);
        $this->assertCount(0, $result);
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Meta\Exception\DescriptorSubjectException
     */
    public function invalidNullSubjectTest()
    {
        $this->fixture->describe(null);
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Meta\Exception\DescriptorSubjectException
     */
    public function invalidArraySubjectTest()
    {
        $this->fixture->describe([]);
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Meta\Exception\DescriptorSubjectException
     */
    public function invalidObjectSubjectTest()
    {
        $this->fixture->describe(new \stdClass());
    }
}
