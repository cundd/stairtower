<?php
declare(strict_types=1);

namespace Cundd\Stairtower\ErrorHandling;


use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\DataAccess\Coordinator;
use Cundd\Stairtower\Domain\Model\DatabaseRawDataInterface;
use Cundd\Stairtower\Memory\Manager;
use Cundd\Stairtower\Utility\GeneralUtility;
use DateTime;

/**
 * Crash handler that tries to rescue the in-memory databases
 */
class CrashHandler implements HandlerInterface
{
    public static $sharedCrashHandler;

    /**
     * Defines if the crash handler should be called on shutdown
     *
     * @var bool
     */
    protected static $isRegistered = false;

    public function __construct()
    {
        self::$sharedCrashHandler = $this;
    }


    /**
     * Registers the crash handler
     */
    public function register()
    {
        register_shutdown_function([$this, 'shutdown']);
        static::$isRegistered = true;
    }


    /**
     * Unregisters the crash handler
     */
    public function unregister()
    {
        static::$isRegistered = false;
    }

    /**
     * Tries to handle a crashed system
     */
    public function shutdown()
    {
        $error = error_get_last();
        if (static::$isRegistered && $error !== null) {
            $errno = intval($error['type']);
            $errstr = $error['message'];
            $errfile = $error['file'];
            $errline = $error['line'];

            $this->handle($errno, $errstr, $errfile, $errline);
        }
    }

    /**
     * Perform the actions to handle the problem
     *
     * @param int    $code
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     * @return bool
     */
    public function handle(int $code, string $message, string $file = '', int $line = 0, $context = [])
    {
        // Construct a helpful crash message
        $errorReport = [];
        $errorReport[] = sprintf(
            'Server crashed with code %d and message "%s" in %s at %s',
            $code,
            $message,
            $file,
            $line
        );
        $errorReport[] = sprintf('Date/time: %s', $this->getTimeWithMicroseconds()->format('Y-m-d H:i:s.u'));
        $errorReport[] = sprintf('Current memory usage: %s', GeneralUtility::formatBytes(memory_get_usage(true)));
        $errorReport[] = sprintf('Peak memory usage: %s', GeneralUtility::formatBytes(memory_get_peak_usage(true)));

        // Try to rescue data
        $errorReport[] = $this->rescueData();

        // Output and save the information
        $errorReport = implode(PHP_EOL, $errorReport);
        $errorReportPath = static::getRescueDirectory() . 'CRASH_REPORT.txt';
        file_put_contents($errorReportPath, $errorReport);
        print $errorReport;

        return true;
    }

    /**
     * Returns the current time with microseconds
     *
     * @return DateTime
     */
    protected function getTimeWithMicroseconds()
    {
        $t = microtime(true);
        $micro = sprintf('%06d', ($t - floor($t)) * 1000000);
        $now = new DateTime(gmdate('Y-m-d H:i:s.') . $micro);

        return $now;
    }

    /**
     * Try to backup data in memory
     *
     * @return string Returns a message describing the result
     */
    public function rescueData()
    {
        $resultMessageParts = [];
        $data = Manager::getObjectsByTag(Coordinator::MEMORY_MANAGER_TAG);
        $backupDirectory = $this->getRescueDirectory();
        if ($data) {
            foreach ($data as $databaseIdentifier => $database) {
                $currentData = null;
                if ($database instanceof DatabaseRawDataInterface) {
                    $currentData = $database->getRawData();
                } elseif ($database instanceof \Iterator) {
                    $currentData = iterator_to_array($database);
                }

                if (!$currentData) {
                    $resultMessageParts[] = sprintf('Can not rescue database %s', $databaseIdentifier);
                    continue;
                }

                $backupData = null;
                $jsonData = json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                if ($jsonData) {
                    $backupData = $jsonData;
                } else {
                    $backupData = serialize($currentData);
                }


                $backupPath = $backupDirectory . $databaseIdentifier . '.' . ($jsonData ? 'json' : 'bin');
                if (file_put_contents($backupPath, $backupData)) {
                    $resultMessageParts[] = sprintf(
                        'Created backup of database %s at %s',
                        $databaseIdentifier,
                        $backupPath
                    );
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