<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.12.14
 * Time: 12:09
 */

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\Constants;

/**
 * ExpandConfiguration test
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
class ExpandConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function expandConfigurationShouldReturnFalseAsDefaultExpandToMany()
    {
        $fixture = new ExpandConfiguration('person', 'person-small');
        $this->assertFalse($fixture->getExpandToMany());
    }

    /**
     * @test
     */
    public function expandConfigurationShouldReturnIdAsDefaultForeignFieldTest()
    {
        $fixture = new ExpandConfiguration('person', 'person-small');
        $this->assertEquals(Constants::DATA_ID_KEY, $fixture->getForeignKey());
    }

    /**
     * @test
     */
    public function expandConfigurationCanHaveAsKeyTest()
    {
        $fixture = new ExpandConfiguration('person', 'person-small');
        $this->assertTrue(!$fixture->getAsKey());

        $fixture = new ExpandConfiguration('person', 'person-small', Constants::DATA_ID_KEY, 'person-data');
        $this->assertEquals('person-data', $fixture->getAsKey());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Expand\Exception\InvalidConfigurationException
     */
    public function expandConfigurationDatabaseMustNotBeEmptyTest()
    {
        new ExpandConfiguration('person', '');
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Expand\Exception\InvalidConfigurationException
     */
    public function expandConfigurationLocalKeyMustNotBeEmptyTest()
    {
        new ExpandConfiguration('', 'person-small');
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Expand\Exception\InvalidConfigurationException
     */
    public function expandConfigurationForeignKeyMustNotBeEmptyTest()
    {
        new ExpandConfiguration('person', 'person-small', '');
    }
}
