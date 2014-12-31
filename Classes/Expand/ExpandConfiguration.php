<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:48
 */

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Expand\Exception\InvalidConfigurationException;
use Cundd\PersistentObjectStore\Immutable;

/**
 * Expand configurations
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
class ExpandConfiguration implements ExpandConfigurationInterface, Immutable
{
    /**
     * Database identifier
     *
     * @var string
     */
    protected $databaseIdentifier = '';

    /**
     * Foreign property key
     *
     * @var string
     */
    protected $foreignKey = '';

    /**
     * Local property key
     *
     * @var string
     */
    protected $localKey = '';

    /**
     * Creates a new configuration
     *
     * @param string $localKey
     * @param string $databaseIdentifier
     * @param string $foreignKey
     */
    function __construct($localKey, $databaseIdentifier, $foreignKey = Constants::DATA_ID_KEY)
    {
        if (!$localKey) {
            throw new InvalidConfigurationException('Local property key must not be empty', 1419938512);
        }
        if (!$databaseIdentifier) {
            throw new InvalidConfigurationException('Database identifier must not be empty', 1419938511);
        }
        if (!$foreignKey) {
            throw new InvalidConfigurationException('Foreign property key must not be empty', 1419938513);
        }

        $this->localKey           = $localKey;
        $this->databaseIdentifier = $databaseIdentifier;
        $this->foreignKey         = $foreignKey;
    }


    /**
     * Returns the identifier of the (foreign) database
     *
     * @return string
     */
    public function getDatabaseIdentifier()
    {
        return $this->databaseIdentifier;
    }

    /**
     * Returns the foreign property key
     *
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * Returns the local property key
     *
     * @return string
     */
    public function getLocalKey()
    {
        return $this->localKey;
    }
}