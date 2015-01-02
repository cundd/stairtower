<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29.12.14
 * Time: 15:48
 */

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Expand\Exception\InvalidExpandBuilderInputException;

/**
 * Expand configuration builder
 *
 * @package Cundd\PersistentObjectStore\Expand
 */
class ExpandConfigurationBuilder implements ExpandConfigurationBuilderInterface
{
    /**
     * Build the Expand configurations from the given definition
     *
     * @param string $expandDefinition Definition as string (E.g. "property1-database1-foreign1/property2-database2-foreign2"
     * @return ExpandConfigurationInterface[]
     */
    public function buildExpandConfigurations($expandDefinition)
    {
        $expandConfigurationCollection = array();
        $expandDefinitionParts = explode(Constants::EXPAND_REQUEST_DELIMITER, $expandDefinition);
        foreach ($expandDefinitionParts as $currentDefinition) {
            $currentDefinitionParts = explode(Constants::EXPAND_REQUEST_SPLIT_CHAR, $currentDefinition);
            if (count($currentDefinitionParts) < 3) {
                throw new InvalidExpandBuilderInputException(
                    sprintf(
                        'Could not split definition \'%s\' into property key, database and foreign key',
                        $currentDefinition
                    ),
                    1420047248
                );
            }

            $expandConfigurationCollection[] = new ExpandConfiguration(
                $currentDefinitionParts[0],
                $currentDefinitionParts[1],
                $currentDefinitionParts[2],
                isset($currentDefinitionParts[3]) ? $currentDefinitionParts[3] : ''
            );
        }
        return $expandConfigurationCollection;
    }
}