<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Expand;

/**
 * Interface for Expand configuration builders
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