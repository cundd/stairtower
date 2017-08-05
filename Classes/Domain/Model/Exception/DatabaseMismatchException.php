<?php
namespace Cundd\PersistentObjectStore\Domain\Model\Exception;

    /**
     * Created by PhpStorm.
     * User: daniel
     * Date: 21.09.14
     * Time: 18:05
     */


/**
 * Exception thrown if a Document instance is added to the database and the database identifier don't match
 */
class DatabaseMismatchException extends InvalidDatabaseIdentifierException
{
}