<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\ValueObject;

use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Immutable;
use Cundd\Stairtower\Utility\GeneralUtility;
use DateTime;
use DateTimeInterface;
use JsonSerializable;

/**
 * Class to describe server statistics
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
     * @var string
     */
    private $eventLoopImplementation;

    /**
     * @param string            $version
     * @param string            $guid
     * @param DateTimeInterface $startTime
     * @param float             $memoryUsage
     * @param float             $memoryPeakUsage
     */
    public function __construct(
        string $guid,
        ?DateTimeInterface $startTime,
        string $eventLoopImplementation
    ) {
        $this->guid = $guid;
        $this->startTime = $startTime;
        $this->memoryPeakUsage = memory_get_usage(true);
        $this->memoryUsage = memory_get_peak_usage(true);
        $this->eventLoopImplementation = $eventLoopImplementation;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $upTime = $this->getUpTime() ? $this->getUpTime()->format('%a days %H:%I:%S') : 'undefined';
        $startTime = $this->getStartTime() ? $this->getStartTime()->format('r') : 'undefined';

        return [
            'version'                 => $this->getVersion(),
            'guid'                    => $this->getGuid(),
            'host'                    => $this->getHost(),
            'startTime'               => $startTime,
            'upTime'                  => $upTime,
            'memoryUsage'             => GeneralUtility::formatBytes($this->getMemoryUsage()),
            'memoryPeakUsage'         => GeneralUtility::formatBytes($this->getMemoryPeakUsage()),
            'os'                      => $this->getOsInformation(),
            'eventLoopImplementation' => $this->getEventLoopImplementation(),
        ];
    }

    /**
     * Returns the implementation name of the Event Loop
     *
     * @return string
     */
    public function getEventLoopImplementation(): string
    {
        return $this->eventLoopImplementation;
    }

    /**
     * Returns the server version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return Constants::VERSION;
    }

    /**
     * Returns the global unique identifier for the server
     *
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }

    /**
     * Returns the time of the server start
     *
     * @return DateTimeInterface|null
     */
    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    /**
     * Returns the server up-time
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
    public function getMemoryUsage(): float
    {
        return $this->memoryUsage;
    }

    /**
     * Returns the peak memory usage
     *
     * @return float
     */
    public function getMemoryPeakUsage(): float
    {
        return $this->memoryPeakUsage;
    }

    /**
     * Return information about the OS
     *
     * @return array
     */
    public function getOsInformation(): array
    {
        return [
            'os' => [
                'vendor'  => php_uname('s'),
                'version' => php_uname('r'),
                'machine' => php_uname('m'),
                'info'    => php_uname('v'),
            ],
        ];
    }

    /**
     * Return the hostname
     *
     * @return string
     */
    public function getHost(): string
    {
        return php_uname('n');
    }
}
