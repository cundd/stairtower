<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 14.10.14
 * Time: 18:26
 */

namespace Cundd\PersistentObjectStore\Utility;


/**
 * Tests for the general utility
 *
 * @package Cundd\PersistentObjectStore\Utility
 */
class GeneralUtilityTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @test
	 */
	public function underscoreToCamelCaseTest() {
		$this->assertEquals('_mySuperMethod', GeneralUtility::underscoreToCamelCase('_my_super_method'));
		$this->assertEquals('_someThingPrivate', GeneralUtility::underscoreToCamelCase('_some_thing_private'));
		$this->assertEquals('thisWouldBeMorePublic', GeneralUtility::underscoreToCamelCase('this_would_be_more_public'));
		$this->assertEquals('butThatsJustNamesAnyway', GeneralUtility::underscoreToCamelCase('but_thats_just_names_anyway'));
		$this->assertEquals('howDoesThisWork4numbers', GeneralUtility::underscoreToCamelCase('how_does_this_work_4numbers'));
		$this->assertEquals('2Good', GeneralUtility::underscoreToCamelCase('2_good'));

	}
}
 