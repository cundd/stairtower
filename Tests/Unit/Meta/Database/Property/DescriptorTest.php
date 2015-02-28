<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 25.02.15
 * Time: 21:14
 */

namespace Cundd\PersistentObjectStore\Meta\Database\Property;

use Cundd\PersistentObjectStore\AbstractDatabaseBasedCase;
use Cundd\PersistentObjectStore\Constants;

/**
 * Test for Descriptor
 *
 * @package Cundd\PersistentObjectStore\Meta\Database
 */
class DescriptorTest extends AbstractDatabaseBasedCase
{
    /**
     * @var Descriptor
     */
    protected $fixture;


    /**
     * @var \Cundd\PersistentObjectStore\DataAccess\Coordinator
     */
    protected $coordinator;

    protected function setUp()
    {
        parent::setUp();
        $this->coordinator = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Coordinator');
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
        //$database = $this->coordinator->getDatabase('people');

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
     * @expectedException \Cundd\PersistentObjectStore\Meta\Exception\DescriptorSubjectException
     */
    public function invalidNullSubjectTest()
    {
        $this->fixture->describe(null);
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Meta\Exception\DescriptorSubjectException
     */
    public function invalidArraySubjectTest()
    {
        $this->fixture->describe(array());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Meta\Exception\DescriptorSubjectException
     */
    public function invalidObjectSubjectTest()
    {
        $this->fixture->describe(new \stdClass());
    }
}
