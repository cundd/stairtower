<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.10.14
 * Time: 12:34
 */

namespace Cundd\PersistentObjectStore\Server\Handler;

interface Event
{
    const DATABASE_CREATED = 'database.created';
    const DATABASE_DELETED = 'database.deleted';

    const DOCUMENT_CREATED = 'document.created';
    const DOCUMENT_UPDATED = 'document.updated';
    const DOCUMENT_DELETED = 'document.deleted';
}
