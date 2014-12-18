<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:39
 */

namespace Cundd\PersistentObjectStore;

use Cundd\PersistentObjectStore\Domain\Model\Document;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;

/**
 * Abstract data based test case
 *
 * @package Cundd\PersistentObjectStore
 */
class AbstractDataBasedCase extends AbstractCase
{
    /**
     * Returns the test data as objects
     *
     * @return array
     */
    public function getAllTestObjects()
    {
        $allTestData    = $this->getAllTestData();
        $allTestObjects = array();
        foreach ($allTestData as $currentTestData) {
            $currentObject    = new Document($currentTestData, 'contacts');
            $allTestObjects[] = $currentObject;
//			$allTestObjects[$currentObject->getGuid()] = $currentObject;
        }
        return $allTestObjects;
    }

    /**
     * Returns the raw test data
     *
     * @return array
     */
    public function getAllTestData()
    {
        return array_map(function ($item) {
            if (isset($item['email'])) {
                $item[Constants::DATA_ID_KEY] = $item['email'];
            }
            return $item;
        }, json_decode(file_get_contents(__DIR__ . '/../Resources/contacts.json'), true));
    }

    /**
     * Returns the number of items in the raw test data
     *
     * @return array
     */
    public function countAllTestData()
    {
        return count($this->getAllTestData());
    }

    /**
     * @param $database
     * @return array
     */
    public function databaseToDataArray($database)
    {
        $foundData = array();

        /** @var DocumentInterface $dataObject */
        foreach ($database as $dataObject) {
            $foundData[] = $dataObject;
        }
        return $foundData;
    }
} 