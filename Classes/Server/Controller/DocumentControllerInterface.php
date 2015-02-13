<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 08.01.15
 * Time: 10:40
 */

namespace Cundd\PersistentObjectStore\Server\Controller;

use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Server\ValueObject\RequestInfo;

/**
 * Interface for Document Controllers
 *
 * @package Cundd\PersistentObjectStore\Server\Controller
 */
interface DocumentControllerInterface extends ControllerInterface
{
    /**
     * Returns the database for the current Request Info
     *
     * @return DatabaseInterface|null
     */
    public function getDatabaseForCurrentRequest();

    /**
     * Returns the database for the given request or null if it is not specified
     *
     * @param RequestInfo $requestInfo
     * @return DatabaseInterface|null
     */
    public function getDatabaseForRequestInfo(RequestInfo $requestInfo);

    /**
     * Returns the Document for the current Request Info
     *
     * @return DocumentInterface|null
     */
    public function getDocumentForCurrentRequest();

    /**
     * Returns the Document for the given request or null if it is not specified
     *
     * @param RequestInfo $requestInfo
     * @return DocumentInterface|null
     */
    public function getDocumentForRequest(RequestInfo $requestInfo);
}