<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.10.14
 * Time: 17:41
 */

namespace Cundd\PersistentObjectStore;


use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\DataAccess\Coordinator;
use Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface;
use Cundd\PersistentObjectStore\Domain\Model\Database;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use DateTime;

/**
 * Crash handler that tries to rescue the in-memory databases
 *
 * @package Cundd\PersistentObjectStore
 */
class CrashHandler
{
    public static $sharedCrashHandler;
    /**
     * Defines if the crash handler has been registered
     *
     * @var bool
     */
    protected static $didRegister = false;
    /**
     * @var CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    public function __construct()
    {
        self::$sharedCrashHandler = $this;
    }


    /**
     * Registers the crash handler
     */
    public function register()
    {
        register_shutdown_function(array($this, 'handleCrash'));
//		register_shutdown_function(array(get_called_class(), 'handleCrash'));
    }

    /**
     * Tries to handle a crashed system
     */
    public function handleCrash()
    {
        $error = error_get_last();
        if ($error !== null) {
            // Construct a helpful crash message
            $errorNumber  = intval($error['type']);
            $errorFile    = $error['file'];
            $errorLine    = $error['line'];
            $errorMessage = $error['message'];

            $errorReport   = [];
            $errorReport[] = sprintf('Server crashed with code %d and message "%s" in %s at %s', $errorNumber,
                $errorMessage, $errorFile, $errorLine);
            $errorReport[] = sprintf('Date/time: %s', $this->getTimeWithMicroseconds()->format('Y-m-d H:i:s.u'));
            $errorReport[] = sprintf('Current memory usage: %s', GeneralUtility::formatBytes(memory_get_usage(true)));
            $errorReport[] = sprintf('Peak memory usage: %s', GeneralUtility::formatBytes(memory_get_peak_usage(true)));

            // Try to rescue data
            $errorReport[] = $this->rescueData();

            // Output and save the information
            $errorReport     = implode(PHP_EOL, $errorReport);
            $errorReportPath = static::getRescueDirectory() . 'CRASH_REPORT.txt';
            file_put_contents($errorReportPath, $errorReport);
            print $errorReport;
        }
    }

    /**
     * Returns the current time with microseconds
     *
     * @return DateTime
     */
    protected function getTimeWithMicroseconds()
    {
        $t     = microtime(true);
        $micro = sprintf('%06d', ($t - floor($t)) * 1000000);
        $now   = new DateTime(gmdate('Y-m-d H:i:s.') . $micro);
        return $now;
    }

    /**
     * Try to backup data in memory
     *
     * @return string Returns a message describing the result
     */
    public function rescueData()
    {
        $resultMessageParts = array();
        $data               = ($this->coordinator instanceof Coordinator) ? $this->coordinator->getObjectStore() : array();
        $backupDirectory    = $this->getRescueDirectory();
        if ($data) {
            foreach ($data as $databaseIdentifier => $database) {
                $currentData = null;
                if ($database instanceof Database) {
                    $currentData = $database->getRawData();
                } elseif ($database instanceof \Iterator) {
                    $currentData = iterator_to_array($database);
                }

                if (!$currentData) {
                    $resultMessageParts[] = sprintf('Can not rescue database %s', $databaseIdentifier);
                    continue;
                }

                $backupData = null;
                $jsonData   = json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                if ($jsonData) {
                    $backupData = $jsonData;
                } else {
                    $backupData = serialize($currentData);
                }


                $backupPath = $backupDirectory . $databaseIdentifier . '.' . ($jsonData ? 'json' : 'bin');
                if (file_put_contents($backupPath, $backupData)) {
                    $resultMessageParts[] = sprintf('Created backup of database %s at %s', $databaseIdentifier,
                        $backupPath);
                } else {
                    $resultMessageParts[] = sprintf('Can not rescue database %s', $databaseIdentifier);
                }
            }
        } else {
            $resultMessageParts[] = sprintf('Can not find any data to rescue');
        }
        return implode(PHP_EOL, $resultMessageParts);
    }

    /**
     * Returns the path to store the rescue data in
     *
     * @return string
     */
    protected function getRescueDirectory()
    {
        $backupDirectory = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('rescuePath');
        $backupDirectory .= gmdate('Y-m-d-H-i-s') . '/';
        if (!file_exists($backupDirectory)) {
            mkdir($backupDirectory, 0770, true);
        }
        return $backupDirectory;
    }
} 