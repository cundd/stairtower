<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24.10.14
 * Time: 21:55
 */

namespace Cundd\PersistentObjectStore\Memory;

use Cundd\PersistentObjectStore\DataAccess\Coordinator;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DatabaseStateInterface;
use Cundd\PersistentObjectStore\Memory\Exception\MemoryException;
use Cundd\PersistentObjectStore\Utility\DebugUtility;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;

/**
 * Helper to check and free memory
 *
 * @package Cundd\PersistentObjectStore\Memory
 */
class Helper
{
    /**
     * Returns the guessed amount of memory needed to parse the given file as JSON
     *
     * @param string $filePath
     * @return int
     */
    public function guessMemoryForJsonFile($filePath)
    {
        $value = filesize($filePath);
        return 15.954 * $value - 2E+07;
    }

    /**
     * Tries to free the given amount of memory
     *
     * @param int $size Memory to free in bytes
     * @return bool Returns if the memory could be freed
     */
    public function freeMemory($size)
    {
        $size          = abs($size);
        $currentMemory = memory_get_usage(true);
        $freedMemory   = 0;
        $databases = Manager::getObjectsByTag(Coordinator::MEMORY_MANAGER_TAG, true);
        foreach ($databases as $identifier => $database) {
            /** @var DatabaseInterface $database */
            if ($database->getState() === DatabaseStateInterface::STATE_CLEAN) {
                Manager::free($identifier);
                $freedMemory += ($currentMemory - memory_get_usage(true));
                $currentMemory = memory_get_usage(true);

                if ($freedMemory >= $size) {
                    return true;
                }
            }
//			DebugUtility::var_dump(gc_enabled(), array_keys(get_defined_vars()), GeneralUtility::formatBytes($currentMemory), GeneralUtility::formatBytes($freedMemory));
        }
//		DebugUtility::var_dump(gc_enabled(), array_keys(get_defined_vars()), GeneralUtility::formatBytes($currentMemory), GeneralUtility::formatBytes($freedMemory));
        return false;
    }

    /**
     * Returns the currently available memory
     *
     * @return int
     */
    public function getAvailableMemory()
    {
        $iniMemoryLimit = ini_get('memory_limit');
        switch (strtoupper(substr($iniMemoryLimit, -1))) {
            case 'G':
                $memoryLimit = intval(substr($iniMemoryLimit, 0, -1)) * 1024 * 1024 * 1024;
                break;

            case 'M':
                $memoryLimit = intval(substr($iniMemoryLimit, 0, -1)) * 1024 * 1024;
                break;

            case 'K':
                $memoryLimit = intval(substr($iniMemoryLimit, 0, -1)) * 1024;
                break;

            default:
                if (!is_numeric(substr($iniMemoryLimit, -1))) {
                    throw new MemoryException(sprintf('Unknown memory unit in %s', $iniMemoryLimit), 1414183267);
                }
                $memoryLimit = intval($iniMemoryLimit);
        }

        return $memoryLimit - memory_get_usage(true);
    }

    /**
     * Checks if the given JSON file can be loaded into memory
     *
     * The method tries to free enough memory if needed
     *
     * @param $filePath
     * @return bool
     */
    public function checkMemoryForJsonFile($filePath)
    {
        $guessedMemory   = $this->guessMemoryForJsonFile($filePath);
        $availableMemory = $this->getAvailableMemory();
//		DebugUtility::pl('Available memory: %s', GeneralUtility::formatBytes($availableMemory));
//		DebugUtility::pl('We will need about %s', GeneralUtility::formatBytes($guessedMemory));
        if ($guessedMemory > $availableMemory) {
            //			DebugUtility::pl('Please free %s bytes', GeneralUtility::formatBytes($guessedMemory - $availableMemory));
            if (!$this->freeMemory($guessedMemory - $availableMemory)) {
                DebugUtility::pl('Required estimated memory amount of %s not available',
                    GeneralUtility::formatBytes($guessedMemory - $availableMemory));

//				throw new MemoryException(sprintf(
//					'Required memory amount of %s not available',
//					GeneralUtility::formatBytes($guessedMemory - $availableMemory))
//				);
            }
        }
    }
}
