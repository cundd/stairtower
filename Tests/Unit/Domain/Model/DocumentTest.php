<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit\Domain\Model;

use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Domain\Model\Document;
use Cundd\Stairtower\Tests\Unit\AbstractCase;

class DocumentTest extends AbstractCase
{
    /**
     * @var \Cundd\Stairtower\Domain\Model\Document
     */
    protected $fixture;

    /**
     * @test
     */
    public function getGuidTest()
    {
        $this->assertEquals('congress_members-1', $this->fixture->getGuid());
    }

    /**
     * @test
     */
    public function getDatabaseIdentifierTest()
    {
        $this->assertEquals('congress_members', $this->fixture->getDatabaseIdentifier());
    }

    /**
     * @test
     */
    public function setDatabaseIdentifierTest()
    {
        $databaseIdentifier = 'a_new_data_identifier';
        $this->fixture->setDatabaseIdentifier($databaseIdentifier);
        $this->assertEquals($databaseIdentifier, $this->fixture->getDatabaseIdentifier());
    }

    /**
     * @test
     */
    public function getIdTest()
    {
        $this->assertEquals(1, $this->fixture->getId());
    }

    /**
     * @test
     */
    public function getDataTest()
    {
        $this->assertInternalType('array', $this->fixture->getData());
    }

    /**
     * @test
     */
    public function setDataTest()
    {
        $data = [
            'name' => 'Daniel Corn',
            'job'  => 'Developer',
        ];
        $this->fixture->setData($data);
        $this->assertEquals($data, $this->fixture->getData());
    }

    /**
     * @test
     */
    public function valueForKeyTest()
    {
        $this->assertEquals(1, $this->fixture->getId());
        $this->assertEquals(
            'Representative for Hawaii\'s 1st congressional district',
            $this->fixture->valueForKey('description')
        );
        $this->assertEquals(1, $this->fixture->valueForKey('district'));
        $this->assertEquals('1986-10-18', $this->fixture->valueForKey('enddate'));
        $this->assertEquals(null, $this->fixture->valueForKey('leadership_title'));
        $this->assertEquals('Democrat', $this->fixture->valueForKey('party'));
    }

    /**
     * @test
     */
    public function setValueForKeyTest()
    {
        $id = 100;
        $this->fixture->setValueForKey($id, 'id');
        $this->assertEquals($id, $this->fixture->valueForKey('id'));

        $this->assertEquals(1, $this->fixture->getId());

        $description = 'Champion';
        $this->fixture->setValueForKey($description, 'description');
        $this->assertEquals($description, $this->fixture->valueForKey('description'));
    }

    /**
     * @test
     */
    public function valueForKeyPathTest()
    {
        $this->assertEquals(1, $this->fixture->valueForKeyPath(Constants::DATA_ID_KEY));
        $this->assertEquals(
            'Representative for Hawaii\'s 1st congressional district',
            $this->fixture->valueForKeyPath('description')
        );
        $this->assertEquals(1, $this->fixture->valueForKeyPath('district'));
        $this->assertEquals('1986-10-18', $this->fixture->valueForKeyPath('enddate'));
        $this->assertEquals(null, $this->fixture->valueForKeyPath('leadership_title'));
        $this->assertEquals('Democrat', $this->fixture->valueForKeyPath('party'));

        $this->assertEquals('Neil', $this->fixture->valueForKeyPath('person.firstname'));
        $this->assertEquals('male', $this->fixture->valueForKeyPath('person.gender'));
        $this->assertEquals('Abercrombie', $this->fixture->valueForKeyPath('person.lastname'));
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Domain\Model\Exception\InvalidDataException
     */
    public function invalidInputDataStringTest()
    {
        new Document('blur');
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Domain\Model\Exception\InvalidDataException
     */
    public function invalidInputDataFloatTest()
    {
        new Document(0.1);
    }

    /**
     * @test
     * @expectedException \Cundd\Stairtower\Domain\Model\Exception\InvalidDataException
     */
    public function invalidInputDataIntTest()
    {
        new Document(1);
    }

    /**
     * @test
     */
    public function ignoreFalseInputDataTest()
    {
        $this->assertEmpty((new Document(false))->getData());
        $this->assertEmpty((new Document(null))->getData());
        $this->assertEmpty((new Document([]))->getData());
        $this->assertEmpty((new Document(''))->getData());
    }

    protected function setUp()
    {
        $this->checkPersonFile();

        $this->setUpXhprof();

        $fixtureJSON = <<<FIXTURE
{
        "_id": 1,
        "congress_numbers": [
            99
        ],
        "current": false,
        "description": "Representative for Hawaii's 1st congressional district",
        "district": 1,
        "enddate": "1986-10-18",
        "leadership_title": null,
        "party": "Democrat",
        "person": {
            "bioguideid": "A000014",
            "birthday": "1938-06-26",
            "cspanid": null,
            "firstname": "Neil",
            "gender": "male",
            "gender_label": "Male",
            "id": 400001,
            "lastname": "Abercrombie",
            "link": "https:\/\/www.govtrack.us\/congress\/members\/neil_abercrombie\/400001",
            "middlename": "",
            "name": "Rep. Neil Abercrombie [D-HI1, 1991-2010]",
            "namemod": "",
            "nickname": "",
            "osid": "N00007665",
            "pvsid": "26827",
            "sortname": "Abercrombie, Neil (Rep.) [D-HI1, 1991-2010]",
            "twitterid": null,
            "youtubeid": null
        },
        "phone": null,
        "role_type": "representative",
        "role_type_label": "Representative",
        "senator_class": null,
        "senator_rank": null,
        "startdate": "1985-01-03",
        "state": "HI",
        "title": "Rep.",
        "title_long": "Representative",
        "website": ""
    }
FIXTURE;


        $document = new Document();
        $document->setData(json_decode($fixtureJSON, true));

        $document->setDatabaseIdentifier('congress_members');

        $this->fixture = $document;
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }


}