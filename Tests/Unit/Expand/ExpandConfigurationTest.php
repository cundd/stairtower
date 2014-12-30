<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.12.14
 * Time: 12:09
 */

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Expand\Exception\InvalidConfigurationException;

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
    public function expandConfigurationShouldReturnIdAsDefaultForeignFieldTest()
    {
        $fixture = new ExpandConfiguration('person-small', 'person');
        $this->assertEquals(Constants::DATA_ID_KEY, $fixture->getForeignKey());
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Expand\Exception\InvalidConfigurationException
     */
    public function expandConfigurationDatabaseMustNotBeEmptyTest()
    {
        new ExpandConfiguration('', 'person');
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Expand\Exception\InvalidConfigurationException
     */
    public function expandConfigurationLocalKeyMustNotBeEmptyTest()
    {
        new ExpandConfiguration('person-small', '');
    }

    /**
     * @test
     * @expectedException \Cundd\PersistentObjectStore\Expand\Exception\InvalidConfigurationException
     */
    public function expandConfigurationForeignKeyMustNotBeEmptyTest()
    {
        new ExpandConfiguration('person-small', 'person', '');
    }
}
