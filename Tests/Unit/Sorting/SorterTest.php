<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.09.14
 * Time: 22:06
 */

namespace Cundd\PersistentObjectStore\Sorting;


use Cundd\PersistentObjectStore\AbstractDataBasedCase;
use Cundd\PersistentObjectStore\DataAccess\Reader;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Utility\DebugUtility;


/**
 * Test for the sorting
 *
 * @package Cundd\PersistentObjectStore\Sorting
 */
class SorterTest extends AbstractDataBasedCase
{
    /**
     * @var \Cundd\PersistentObjectStore\Sorting\Sorter
     */
    protected $fixture;

    /**
     * @var \Cundd\PersistentObjectStore\DataAccess\Coordinator
     */
    protected $coordinator;

    /**
     * @test
     */
    public function sortPersonsByLatitudeTest()
    {
        $this->checkPersonFile();

        /** @var Database $database */
        $database = $this->coordinator->getDatabase('people');

        $start          = microtime(true);
        $sortedDatabase = $this->fixture->sortCollectionByPropertyKeyPath($database, 'latitude');
        $end            = microtime(true);
//		printf("Sort %0.8f\n", $end - $start);

        $maxIterations = 100;
        $maxIterations = $database->count();

        // TODO: Make this work
        // $this->assertEquals($database->count(), $sortedDatabase->count());

//		var_dump($sortedDatabase[$sortedDatabase->count() - 1]);

        $lastLatitude = -PHP_INT_MAX;
        for ($i = 0; $i < $maxIterations; $i++) {
            /** @var DocumentInterface $item */
            $item = $sortedDatabase[$i];
            $this->assertNotNull($item);

//			printf('%d: Last latitude %0.9f to current %0.9f' . PHP_EOL, $i, $lastLatitude, $item->valueForKey('latitude'));

            $this->assertGreaterThan($lastLatitude, $item->valueForKey('latitude'),
                'Current latitude is not bigger than last for loop number ' . $i);
            $lastLatitude = $item->valueForKey('latitude');
        }


//		$sortedDatabase = $this->fixture->sortCollectionByPropertyKeyPath($database, 'latitude', TRUE);
//
//		$this->assertEquals($database->count(), $sortedDatabase->count());
//
//		$lastLatitude = PHP_INT_MAX;
//		for ($i = 0; $i < $maxIterations; $i++) {
//			/** @var DocumentInterface $item */
//			$item = $sortedDatabase[$i];
//			$this->assertNotNull($item);
//
//			$this->assertLessThan($lastLatitude, $item->valueForKey('latitude'));
//			$lastLatitude = $item->valueForKey('latitude');
//		}
    }

    /**
     * @test
     */
    public function sortPersonsByLatitudeAfterAddingAPersonTest()
    {
        $this->checkPersonFile();

        /** @var Reader $databaseReader */
        $databaseReader      = $this->getDiContainer()->get('\Cundd\PersistentObjectStore\DataAccess\Reader');
        $newlyLoadedDatabase = $databaseReader->loadDatabase('people');
        $dataInstance        = new Document();
        $dataInstance->setData(array(
            '_id'           => '541f004ef8f4d2df32ca60c2',
            'index'         => 5000,
            'isActive'      => false,
            'balance'       => '$2,925.56',
            'picture'       => 'http://placehold.it/32x32',
            'age'           => 31,
            'eyeColor'      => 'brown',
            'name'          => 'Daniel Corn',
            'gender'        => 'male',
            'company'       => 'FARMEX',
            'email'         => 'info-new@cundd.net',
            'phone'         => '+1 (973) 480-3194',
            'address'       => '125 Stone Avenue, Worton, Alabama, 6669',
            'about'         => 'Dolore in excepteur nisi dolor laboris ipsum proident cupidatat proident. Aliquip commodo culpa adipisicing ullamco ad. Ut ex duis tempor do id enim. Proident exercitation officia veniam magna mollit nostrud duis do qui reprehenderit. Ea culpa anim ullamco aliqua culpa nulla ex nisi irure qui incididunt reprehenderit. Labore do velit amet duis aute occaecat. Et sunt ex Lorem qui do deserunt ullamco labore.\r\n',
            'registered'    => '2014-06-29T15:29:47 -02:00',
            'latitude'      => 52.372840,
            'longitude'     => -70.88927,
            'tags'          => [
                'id',
                'consequat',
                'aute',
                'deserunt',
                'in',
                'enim',
                'veniam'
            ],
            'friends'       => [
                array(
                    'id'   => 0,
                    'name' => 'Bray Ruiz'
                ),
                array(
                    'id'   => 1,
                    'name' => 'Carr Kerr'
                ),
                array(
                    'id'   => 2,
                    'name' => 'Carter Dejesus'
                )
            ],
            'greeting'      => 'Hello, Conway Burch! You have 3 unread messages.',
            'favoriteFruit' => 'apple'
        ));

        $newlyLoadedDatabase->add($dataInstance);


        /** @var Database $database */
        $database = $this->coordinator->getDatabase('people');

//		$start = microtime(TRUE);
        $sortedDatabase = $this->fixture->sortCollectionByPropertyKeyPath($newlyLoadedDatabase, 'latitude');
//		$end = microtime(TRUE);
//		printf("Sort %0.8f\n", $end - $start);

        $maxIterations = 100;
        $maxIterations = $database->count();

        // TODO: Make this work
        // $this->assertEquals($database->count(), $sortedDatabase->count());
        // $this->assertNotEquals($database->count(), $newlyLoadedDatabase->count());


//		var_dump($sortedDatabase[$sortedDatabase->count() - 1]);

        $lastLatitude = -PHP_INT_MAX;
        for ($i = 0; $i < $maxIterations; $i++) {
            /** @var DocumentInterface $item */
            $item = $sortedDatabase[$i];
            $this->assertNotNull($item);

//			printf('%d: Last latitude %0.9f to current %0.9f' . PHP_EOL, $i, $lastLatitude, $item->valueForKey('latitude'));

            if ($lastLatitude === $item->valueForKey('latitude')) {
                DebugUtility::var_dump($i, $item, $sortedDatabase[$i - 1]);
            }
            $this->assertGreaterThan($lastLatitude, $item->valueForKey('latitude'),
                'Current latitude is not bigger than last for loop number ' . $i);
            $lastLatitude = $item->valueForKey('latitude');
        }


//		$sortedDatabase = $this->fixture->sortCollectionByPropertyKeyPath($database, 'latitude', TRUE);
//
//		$this->assertEquals($database->count(), $sortedDatabase->count());
//
//		$lastLatitude = PHP_INT_MAX;
//		for ($i = 0; $i < $maxIterations; $i++) {
//			/** @var DocumentInterface $item */
//			$item = $sortedDatabase[$i];
//			$this->assertNotNull($item);
//
//			$this->assertLessThan($lastLatitude, $item->valueForKey('latitude'));
//			$lastLatitude = $item->valueForKey('latitude');
//		}
    }

    /**
     * @test
     */
    public function sortPersonsByLatitudeWithCallbackTest()
    {
        $this->checkPersonFile();

        /** @var Database $database */
        $database = $this->coordinator->getDatabase('people');

        $start          = microtime(true);
        $sortedDatabase = $this->fixture->sortCollectionByCallback($database, function ($itemA, $itemB) {
            /** @var DocumentInterface $itemA */
            /** @var DocumentInterface $itemB */
            $latA = $itemA->valueForKey('latitude');
            $latB = $itemB->valueForKey('latitude');

//			printf('Compare %s to %s', $latA, $latB);
//			var_dump($latA);
            if ($latA == $latB) {
                return 0;
            }
            return ($latA < $latB) ? -1 : 1;
        });
        $end            = microtime(true);
//		printf("Sort %0.8f\n", $end - $start);

        $maxIterations = 100;
//		$maxIterations = $database->count();

        // TODO: Make this work
        // $this->assertEquals($database->count(), $sortedDatabase->count());

        $lastLatitude = -PHP_INT_MAX;
        for ($i = 0; $i < $maxIterations; $i++) {
            /** @var DocumentInterface $item */
            $item = $sortedDatabase[$i];
            $this->assertNotNull($item);

//			printf('%d: Last latitude %0.9f to current %0.9f' . PHP_EOL, $i, $lastLatitude, $item->valueForKey('latitude'));

            $this->assertGreaterThan($lastLatitude, $item->valueForKey('latitude'),
                'Current latitude is not bigger than last for loop number ' . $i);
            $lastLatitude = $item->valueForKey('latitude');
        }
    }

    /**
     * @test
     */
    public function sortPersonsByDistanceWithCallbackTest()
    {
        $this->checkPersonFile();

        /** @var Database $database */
        $database = $this->coordinator->getDatabase('people');


        // Sort the people database by comparing the persons distance to me (47.235934, 9.599398)
        // Nearer persons should appear first

        $myLatitude  = 47.235934;
        $myLongitude = 9.599398;

        $sortedDatabase = $this->fixture->sortCollectionByCallback($database,
            function ($itemA, $itemB) use ($myLongitude, $myLatitude) {
                /** @var DocumentInterface $itemA */
                /** @var DocumentInterface $itemB */
                $distanceA = SorterTest::distance(
                    $itemA->valueForKey('latitude'),
                    $itemA->valueForKey('longitude'),
                    $myLatitude,
                    $myLongitude
                );
                $distanceB = SorterTest::distance(
                    $itemB->valueForKey('latitude'),
                    $itemB->valueForKey('longitude'),
                    $myLatitude,
                    $myLongitude
                );
                if ($distanceA == $distanceB) {
                    return 0;
                }
                return ($distanceA < $distanceB) ? -1 : 1;
            });

        $maxIterations = 100;
//		$maxIterations = $database->count();

        // TODO: Make this work
        // $this->assertEquals($database->count(), $sortedDatabase->count());

        $lastDistance = 0;
        for ($i = 0; $i < $maxIterations; $i++) {
            /** @var DocumentInterface $item */
            $item = $sortedDatabase[$i];
            $this->assertNotNull($item);

            $currentDistance = $distanceA = self::distance(
                $item->valueForKey('latitude'),
                $item->valueForKey('longitude'),
                $myLatitude,
                $myLongitude
            );

            $this->assertGreaterThanOrEqual($lastDistance, $currentDistance,
                'Current distance is not bigger than or equal to last for loop number ' . $i);
            $lastDistance = $currentDistance;
        }


//		$sortedDatabase = $this->fixture->sortCollectionByPropertyKeyPath($database, 'latitude', TRUE);
//
//		$lastLatitude = PHP_INT_MAX;
//		for ($i = 0; $i < 100; $i++) {
//			/** @var DocumentInterface $item */
//			$item = $sortedDatabase[$i++];
//			$this->assertNotNull($item);
//
//			$this->assertLessThan($lastLatitude, $item->valueForKey('latitude'));
//			$lastLatitude = $item->valueForKey('latitude');
//		}
    }

    static public function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K')
    {

        $theta = $lon1 - $lon2;
        $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit  = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else {
            if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }

    protected function setUp()
    {
        $this->checkPersonFile();

//		$this->setUpXhprof();

        $this->coordinator = $this->getDiContainer()->get('Cundd\\PersistentObjectStore\\DataAccess\\Coordinator');
        $this->fixture     = new Sorter();
    }


    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
    /*::                                                                         :*/
    /*::  This routine calculates the distance between two points (given the     :*/
    /*::  latitude/longitude of those points). It is being used to calculate     :*/
    /*::  the distance between two locations using GeoDataSource(TM) Products    :*/
    /*::                     													 :*/
    /*::  Definitions:                                                           :*/
    /*::    South latitudes are negative, east longitudes are positive           :*/
    /*::                                                                         :*/
    /*::  Passed to function:                                                    :*/
    /*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
    /*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
    /*::    unit = the unit you desire for results                               :*/
    /*::           where: 'M' is statute miles                                   :*/
    /*::                  'K' is kilometers (default)                            :*/
    /*::                  'N' is nautical miles                                  :*/
    /*::  Worldwide cities and other features databases with latitude longitude  :*/
    /*::  are available at http://www.geodatasource.com                          :*/
    /*::                                                                         :*/
    /*::  For enquiries, please contact sales@geodatasource.com                  :*/
    /*::                                                                         :*/
    /*::  Official Web site: http://www.geodatasource.com                        :*/
    /*::                                                                         :*/
    /*::         GeoDataSource.com (C) All Rights Reserved 2014		   		     :*/
    /*::                                                                         :*/
    /*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/

    protected function tearDown()
    {
        unset($this->fixture);
//		unset($this->coordinator);
    }

}
