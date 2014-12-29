<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:48
 */

namespace Cundd\PersistentObjectStore\Expand;

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
     * @param string $databaseIdentifier
     * @param string $localKey
     * @param string $foreignKey
     */
    function __construct($databaseIdentifier, $localKey, $foreignKey)
    {
        $this->databaseIdentifier = $databaseIdentifier;
        $this->localKey           = $localKey;
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