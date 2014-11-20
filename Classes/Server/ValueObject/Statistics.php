<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 13.10.14
 * Time: 12:03
 */

namespace Cundd\PersistentObjectStore\Server\ValueObject;

use Cundd\PersistentObjectStore\Immutable;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use DateTime;
use JsonSerializable;

/**
 * Class to describe server statistics
 *
 * @package Cundd\PersistentObjectStore\Server\ValueObject
 */
class Statistics implements Immutable, JsonSerializable
{
    /**
     * Server version number
     *
     * @var string
     */
    protected $version;

    /**
     * Global unique identifier for the server
     *
     * @var string
     */
    protected $guid;

    /**
     * Time of the server start
     *
     * @var DateTime
     */
    protected $startTime;

    /**
     * Current memory usage
     *
     * @var float
     */
    protected $memoryUsage;

    /**
     * Peak memory usage
     *
     * @var float
     */
    protected $memoryPeakUsage;

    /**
     * @param string   $version
     * @param string   $guid
     * @param DateTime $startTime
     * @param float    $memoryUsage
     * @param float    $memoryPeakUsage
     */
    function __construct($version, $guid, $startTime, $memoryUsage, $memoryPeakUsage)
    {
        $this->guid            = $guid;
        $this->memoryPeakUsage = $memoryPeakUsage;
        $this->memoryUsage     = $memoryUsage;
        $this->startTime       = $startTime;
        $this->version         = $version;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return array(
            'version'         => $this->getVersion(),
            'guid'            => $this->getGuid(),
            'startTime'       => $this->getStartTime() ? $this->getStartTime()->format('r') : 'undefined',
            'upTime'          => $this->getUpTime() ? $this->getStartTime()->format('r') : 'undefined',
            'memoryUsage'     => GeneralUtility::formatBytes($this->getMemoryUsage()),
            'memoryPeakUsage' => GeneralUtility::formatBytes($this->getMemoryPeakUsage()),
        );
    }

    /**
     * Returns the server version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Returns the global unique identifier for the server
     *
     * @return string
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * Returns the time of the server start
     *
     * @return DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Returns the server upTime
     *
     * @return bool|\DateInterval
     */
    public function getUpTime()
    {
        $now = new DateTime();
        return $now->diff($this->getStartTime());
    }

    /**
     * Returns the current memory usage
     *
     * @return float
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }

    /**
     * Returns the peak memory usage
     *
     * @return float
     */
    public function getMemoryPeakUsage()
    {
        return $this->memoryPeakUsage;
    }
}