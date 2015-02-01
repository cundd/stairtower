<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 14.10.14
 * Time: 18:26
 */

namespace Cundd\PersistentObjectStore\Utility;


/**
 * Tests for the class loader utility
 *
 * @package Cundd\PersistentObjectStore\Utility
 */
class ClassLoaderUtilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function classExistsWithCacheTest()
    {
        $this->assertTrue(ClassLoaderUtility::classExists(__CLASS__), sprintf('Class %s should exist', __CLASS__));
        $this->assertFalse(ClassLoaderUtility::classExists(__CLASS__ . 'notExists'),
            sprintf('Class %snotExists should not exist', __CLASS__));
        $this->assertTrue(ClassLoaderUtility::classExists('PHPUnit_Framework_TestCase'),
            'Class PHPUnit_Framework_TestCase should exist');

        $this->assertTrue(ClassLoaderUtility::classExists(strtolower(__CLASS__)),
            sprintf('Class %s should exist', __CLASS__));
        $this->assertFalse(ClassLoaderUtility::classExists(strtolower(__CLASS__ . 'notExists')),
            sprintf('Class %snotExists should not exist', __CLASS__));
        $this->assertTrue(ClassLoaderUtility::classExists('phpunit_framework_testcase'),
            'Class PHPUnit_Framework_TestCase should exist');

        $newClassName = 'ClassLoaderUtilityTest_NewClassForClassExistsWithCacheTest';
        $this->assertFalse(ClassLoaderUtility::classExists($newClassName),
            sprintf('Class %s should not exist', __CLASS__));
        eval ("class $newClassName {}");
        $this->assertFalse(ClassLoaderUtility::classExists($newClassName),
            sprintf('Class %s should not exist', __CLASS__));
    }

    /**
     * @test
     */
    public function classExistsWithoutCacheTest()
    {
        $this->assertTrue(ClassLoaderUtility::classExists(__CLASS__, false),
            sprintf('Class %s should exist', __CLASS__));
        $this->assertFalse(ClassLoaderUtility::classExists(__CLASS__ . 'notExists', false),
            sprintf('Class %snotExists should not exist', __CLASS__));
        $this->assertTrue(ClassLoaderUtility::classExists('PHPUnit_Framework_TestCase', false),
            'Class PHPUnit_Framework_TestCase should exist');

        $this->assertTrue(ClassLoaderUtility::classExists(strtolower(__CLASS__)),
            sprintf('Class %s should exist', __CLASS__));
        $this->assertFalse(ClassLoaderUtility::classExists(strtolower(__CLASS__ . 'notExists')),
            sprintf('Class %snotExists should not exist', __CLASS__));
        $this->assertTrue(ClassLoaderUtility::classExists('phpunit_framework_testcase'),
            'Class PHPUnit_Framework_TestCase should exist');

        $newClassName = 'ClassLoaderUtilityTest_NewClassForClassExistsWithoutCacheTest';
        $this->assertFalse(ClassLoaderUtility::classExists($newClassName, false),
            sprintf('Class %s should not exist', __CLASS__));
        eval ("class $newClassName {}");
        $this->assertTrue(ClassLoaderUtility::classExists($newClassName, false),
            sprintf('Class %s should exist', __CLASS__));
    }

    /**
     * @test
     */
    public function clearCacheTest()
    {
        $this->assertTrue(ClassLoaderUtility::classExists(__CLASS__), sprintf('Class %s should exist', __CLASS__));

        ClassLoaderUtility::clearClassCache();
        $this->assertFalse(ClassLoaderUtility::classExists(__CLASS__ . 'notExists'),
            sprintf('Class %snotExists should not exist', __CLASS__));

        ClassLoaderUtility::clearClassCache();
        $this->assertTrue(ClassLoaderUtility::classExists('PHPUnit_Framework_TestCase'),
            'Class PHPUnit_Framework_TestCase should exist');

        ClassLoaderUtility::clearClassCache();
        $this->assertTrue(ClassLoaderUtility::classExists(strtolower(__CLASS__)),
            sprintf('Class %s should exist', __CLASS__));

        ClassLoaderUtility::clearClassCache();
        $this->assertFalse(ClassLoaderUtility::classExists(strtolower(__CLASS__ . 'notExists')),
            sprintf('Class %snotExists should not exist', __CLASS__));

        ClassLoaderUtility::clearClassCache();
        $this->assertTrue(ClassLoaderUtility::classExists('phpunit_framework_testcase'),
            'Class PHPUnit_Framework_TestCase should exist');

        $newClassName = 'ClassLoaderUtilityTest_NewClassForClearCacheTest';
        $this->assertFalse(ClassLoaderUtility::classExists($newClassName),
            sprintf('Class %s should not exist', __CLASS__));
        eval ("class $newClassName {}");

        ClassLoaderUtility::clearClassCache();
        $this->assertTrue(ClassLoaderUtility::classExists($newClassName), sprintf('Class %s should exist', __CLASS__));
    }
}
 