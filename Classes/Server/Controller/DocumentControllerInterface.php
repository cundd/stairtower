<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Controller;

use Cundd\Stairtower\Domain\Model\DatabaseInterface;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Cundd\Stairtower\Server\ValueObject\RequestInterface;

/**
 * Interface for Document Controllers
 */
interface DocumentControllerInterface extends ControllerInterface
{
    /**
     * Returns the database for the current Request
     *
     * @return DatabaseInterface|null
     */
    public function getDatabaseForCurrentRequest(): ?DatabaseInterface;

    /**
     * Returns the database for the given request or null if it is not specified
     *
     * @param RequestInterface $request
     * @return DatabaseInterface|null
     */
    public function getDatabaseForRequest(RequestInterface $request): ?DatabaseInterface;

    /**
     * Returns the Document for the current Request
     *
     * @return DocumentInterface|null
     */
    public function getDocumentForCurrentRequest(): ?DocumentInterface;

    /**
     * Returns the Document for the given request or null if it is not specified
     *
     * @param RequestInterface $request
     * @return DocumentInterface|null
     */
    public function getDocumentForRequest(RequestInterface $request): ?DocumentInterface;
}