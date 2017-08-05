<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Server\Session;

use Cundd\PersistentObjectStore\KeyValueCodingInterface;

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
