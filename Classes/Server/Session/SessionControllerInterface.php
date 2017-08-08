<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Session;


use Cundd\Stairtower\Server\Controller\MutableControllerResultInterface;

/**
 * Interface for Session based controllers
 */
interface SessionControllerInterface
{
    /**
     * Returns the session loaded for the current request, or newly created one
     *
     * @return SessionInterface|null
     */
    public function getSession();

    /**
     * Returns a mutable Controller Result instance
     *
     * This may be used to inject session cookies into the response
     *
     * @return MutableControllerResultInterface
     */
    public function buildResponse();
}