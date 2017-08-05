<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Utility;

use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDatabaseIdentifierException;
use Cundd\PersistentObjectStore\Domain\Model\Exception\InvalidDataIdentifierException;

class DummyObjectThatCanBeConvertedToString
{
    protected $stringData = '';

    function __construct($stringData)
    {
        $this->stringData = $stringData;
    }


    function __toString()
    {
        return $this->stringData;
    }


}

/**
 * Tests for the general utility
 */
class GeneralUtilityTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function assertDatabaseIdentifierTest()
    {
        GeneralUtility::assertDatabaseIdentifier('database');
        GeneralUtility::assertDatabaseIdentifier('my-database');
        GeneralUtility::assertDatabaseIdentifier('my_database');

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('my:database');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('my.database');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('this/folder');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('..');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('.');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('info@cundd.net');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('543fb69d448766d1eeb2c62a');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('_543fb69d448766d1eeb2c62a');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('@543fb69d448766d1eeb2c62a');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('.543fb69d448766d1eeb2c62a');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('-543fb69d448766d1eeb2c62a');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('_543fb69d448766d1eeb2c62a');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('ä43fbä69d448766d1eeb2c62a');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDatabaseIdentifier('43fbä69d448766d1eeb2c62a');
        } catch (InvalidDatabaseIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);
    }

    /**
     * @test
     */
    public function assertDataIdentifierTest()
    {
        GeneralUtility::assertDataIdentifier('daniel');
        GeneralUtility::assertDataIdentifier('info@cundd.net');
        GeneralUtility::assertDataIdentifier('543fb69d448766d1eeb2c62a');

        $crashed = false;
        try {
            GeneralUtility::assertDataIdentifier('');
        } catch (InvalidDataIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDataIdentifier('_543fb69d448766d1eeb2c62a');
        } catch (InvalidDataIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDataIdentifier('@543fb69d448766d1eeb2c62a');
        } catch (InvalidDataIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDataIdentifier('.543fb69d448766d1eeb2c62a');
        } catch (InvalidDataIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDataIdentifier('-543fb69d448766d1eeb2c62a');
        } catch (InvalidDataIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDataIdentifier('_543fb69d448766d1eeb2c62a');
        } catch (InvalidDataIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDataIdentifier('ä43fbä69d448766d1eeb2c62a');
        } catch (InvalidDataIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);

        $crashed = false;
        try {
            GeneralUtility::assertDataIdentifier('43fbä69d448766d1eeb2c62a');
        } catch (InvalidDataIdentifierException $exception) {
            $crashed = true;
        }
        $this->assertTrue($crashed);
    }

    /**
     * @test
     */
    public function underscoreToCamelCaseTest()
    {
        $this->assertEquals('_mySuperMethod', GeneralUtility::underscoreToCamelCase('_my_super_method'));
        $this->assertEquals('_someThingPrivate', GeneralUtility::underscoreToCamelCase('_some_thing_private'));
        $this->assertEquals(
            'thisWouldBeMorePublic',
            GeneralUtility::underscoreToCamelCase('this_would_be_more_public')
        );
        $this->assertEquals(
            'butThatsJustNamesAnyway',
            GeneralUtility::underscoreToCamelCase('but_thats_just_names_anyway')
        );
        $this->assertEquals(
            'howDoesThisWork4numbers',
            GeneralUtility::underscoreToCamelCase('how_does_this_work_4numbers')
        );
        $this->assertEquals('2Good', GeneralUtility::underscoreToCamelCase('2_good'));
    }

    /**
     * @test
     */
    public function toStringTest()
    {
        $this->assertSame('Jesus saved my life', GeneralUtility::toString('Jesus saved my life'));
        $this->assertSame('Jesus saved my life', GeneralUtility::toString(['Jesus', 'saved', 'my', 'life']));
        $this->assertSame('1', GeneralUtility::toString(1));
        $this->assertSame('0', GeneralUtility::toString(0));
        $this->assertSame('1', GeneralUtility::toString(true));
        $this->assertSame('', GeneralUtility::toString(false));
        $this->assertSame('', GeneralUtility::toString(null));
        $this->assertSame('NAN', GeneralUtility::toString(sqrt(-1.0)));

        $tempFile = tmpfile();
        $this->assertContains('Resource id ', GeneralUtility::toString($tempFile));
        fclose($tempFile);

        $dataInstance = new Document(['my' => 'life']);
        $this->assertFalse(GeneralUtility::toString($dataInstance));

        $object = new DummyObjectThatCanBeConvertedToString('my life');
        $this->assertSame('my life', GeneralUtility::toString($object));

    }

    /**
     * @test
     */
    public function getTypeTest()
    {
        $this->assertSame('string', GeneralUtility::getType('Jesus saved my life'));
        $this->assertSame('array', GeneralUtility::getType(['Jesus', 'saved', 'my', 'life']));
        $this->assertSame('integer', GeneralUtility::getType(1));
        $this->assertSame('integer', GeneralUtility::getType(0));
        $this->assertSame('boolean', GeneralUtility::getType(true));
        $this->assertSame('boolean', GeneralUtility::getType(false));
        $this->assertSame('NULL', GeneralUtility::getType(null));
        $this->assertSame('double', GeneralUtility::getType(1.0));
        $this->assertSame('double', GeneralUtility::getType(-1.0));
        $this->assertSame('double', GeneralUtility::getType(sqrt(-1.0)));

        $tempFile = tmpfile();
        $this->assertContains('resource', GeneralUtility::getType($tempFile));
        fclose($tempFile);

        $dataInstance = new Document(['my' => 'life']);
        $this->assertSame(
            'Cundd\\PersistentObjectStore\\Domain\\Model\\Document',
            GeneralUtility::getType($dataInstance)
        );

        $object = new DummyObjectThatCanBeConvertedToString('my life');
        $this->assertSame(
            'Cundd\\PersistentObjectStore\\Utility\\DummyObjectThatCanBeConvertedToString',
            GeneralUtility::getType($object)
        );
    }
}
 