<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Expand;

use Cundd\PersistentObjectStore\Constants;
use Cundd\PersistentObjectStore\Expand\Exception\InvalidExpandBuilderInputException;

/**
 * Expand configuration builder
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
        $expandConfigurationCollection = [];
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
            list($localKey, $databaseIdentifier, $foreignKey) = $currentDefinitionParts;
            $asKey = isset($currentDefinitionParts[3]) ? $currentDefinitionParts[3] : '';

            $expandToMany = false;
            if (substr($localKey, -1) === Constants::EXPAND_REQUEST_TO_MANY) {
                $localKey = substr($localKey, 0, -1);
                $expandToMany = true;
            }
            $expandConfigurationCollection[] = new ExpandConfiguration(
                $localKey,
                $databaseIdentifier,
                $foreignKey,
                $asKey,
                $expandToMany
            );
        }

        return $expandConfigurationCollection;
    }
}