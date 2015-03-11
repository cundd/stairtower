<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:48
 */

namespace Cundd\PersistentObjectStore\Expand;

/**
 * Interface for Expand configuration builders
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
interface ExpandConfigurationBuilderInterface
{
    /**
     * Build the Expand configurations from the given definition
     *
     * @param array $expandDefinition
     * @return ExpandConfigurationInterface[]
     */
    public function buildExpandConfigurations($expandDefinition);
}
