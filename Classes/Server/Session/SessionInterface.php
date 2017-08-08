<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Session;

use Cundd\Stairtower\KeyValueCodingInterface;

/**
 * Interface for session based classes
 */
interface SessionInterface extends KeyValueCodingInterface
{
    /**
     * Returns the session identifier
     *
     * @return string
     */
    public function getIdentifier();
}
