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
     * The "as" property key
     *
     * @var string
     */
    protected $asKey = null;

    /**
     * Defines if the property will have more than one connected Document
     *
     * @var bool
     */
    protected $expandToMany = false;

    /**
     * Creates a new configuration
     *
     * @param string $localKey
     * @param string $databaseIdentifier
     * @param string $foreignKey
     * @param string $asKey
     * @param bool   $expandToMany
     */
    function __construct(
        $localKey,
        $databaseIdentifier,
        $foreignKey = Constants::DATA_ID_KEY,
        $asKey = null,
        $expandToMany = false
    ) {
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
        $this->asKey = $asKey;
        $this->expandToMany = $expandToMany ? true : false;
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

    /**
     * Returns the "as" property key
     *
     * If defined this property key will be filled with the foreign data
     *
     * @return string
     */
    public function getAsKey()
    {
        return $this->asKey;
    }

    /**
     * Returns if the property will have more than one connected Document
     *
     * @return bool
     */
    public function getExpandToMany()
    {
        return $this->expandToMany;
    }
}