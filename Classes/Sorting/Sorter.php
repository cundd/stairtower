<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 14:34
 */

namespace Cundd\PersistentObjectStore\Sorting;


use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\KeyValueCodingInterface;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\ObjectUtility;
use Doctrine\DBAL\Driver;
use SplFixedArray;

/**
 * Class to sort a collection of objects
 *
 * @package Cundd\PersistentObjectStore\Sorting
 */
class Sorter {
	/**
	 * Sorting flags
	 *
	 * @see http://php.net/manual/de/function.sort.php
	 * @var int
	 */
	protected $sortFlags = SORT_REGULAR;

	/**
	 * Returns the sorting flags
	 *
	 * @see http://php.net/manual/de/function.sort.php
	 * @return int
	 */
	public function getSortFlags() {
		return $this->sortFlags;
	}

	/**
	 * Sets the sorting flags
	 *
	 * @see http://php.net/manual/de/function.sort.php
	 * @param int $sortFlags
	 * @return $this
	 */
	public function setSortFlags($sortFlags) {
		$this->sortFlags = $sortFlags;
		return $this;
	}


	/**
	 * Sort the collection of objects by the given key
	 *
	 * @param Database|\Iterator|array $collection
	 * @param string                   $keyPath
	 * @param bool                     $descending
	 * @return \SplFixedArray
	 */
	public function sortCollectionByPropertyKeyPath($collection, $keyPath, $descending = FALSE) {
		$start = microtime(TRUE);


		if (is_array($collection)) {
			$dataCollectionRaw = SplFixedArray::fromArray($collection);
		} else if ($collection instanceof Database) {
//			$dataCollectionRaw = $collection->getRawData();
			$dataCollectionRaw = $collection->prepareAll();
		} else {
			$dataCollectionRaw = SplFixedArray::fromArray(iterator_to_array($collection));
		}
		$dataCollectionCount = $dataCollectionRaw->getSize();

		$end = microtime(TRUE);
		DebugUtility::pl("Get: %0.6f\n", $end - $start);

		$resultArray = array();


		$start = microtime(TRUE);

		$i = 0;
		while ($i < $dataCollectionCount) {
			$item = $dataCollectionRaw[$i];

			if ($item instanceof KeyValueCodingInterface) {
				$propertyValue = $item->valueForKeyPath($keyPath);
			} else {
				$propertyValue = ObjectUtility::valueForKeyPathOfObject($keyPath, $item);
			}
			$resultArray[$propertyValue] = $item;
			$i++;
		}
		$end = microtime(TRUE);
		DebugUtility::pl("Prepare: %0.6f\n", $end - $start);


		if (!$descending) {
			$result = ksort($resultArray, $this->sortFlags);
		} else {
			$result = krsort($resultArray, $this->sortFlags);
		}
		if (!$result) {
			throw new \UnexpectedValueException('Could not sort the database', 1412021636);
		}

//		if ($collection instanceof Database) {
//			Dynamic
//		}
		return SortResult::fromArray(array_values($resultArray));
	}
}