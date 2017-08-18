<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Meta\Database\Property;

use Cundd\Stairtower\Tests\Unit\AbstractDatabaseBasedCase;
use Cundd\Stairtower\Constants;

/**
 * Test for Descriptor
 */
class DescriptorTest extends AbstractDatabaseBasedCase
{
    /**
     * @var Descriptor
     */
    protected $fixture;


    /**
     * @var \Cundd\Stairtower\DataAccess\Coordinator
     */
    protected $coordinator;

    protected function setUp()
    {
        parent::setUp();
        $this->coordinator = $this->getDiContainer()->get('\Cundd\Stairtower\DataAccess\Coordinator');
        $this->fixture     = new Descriptor();
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

        $result = $this->fixture->describe($database);
        $this->assertInternalType('array', $result);
        $this->assertEquals(21, count($result));

        /** @var Description $description */
        foreach ($result as $description) {
            if ($description->getKey() === Constants::DATA_ID_KEY) {
                break;
            }
        }
        $this->assertEquals(Constants::DATA_ID_KEY, $description->getKey());
        $this->assertContains(Description::TYPE_STRING, $description->getTypes());
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
        $this->fixture->describe(array());
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
