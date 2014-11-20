<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 15.10.14
 * Time: 12:38
 */

namespace Cundd\PersistentObjectStore\DataAccess;


interface Event
{
    const DATABASE_CREATED = 'database.created';
    const DATABASE_DROPPED = 'database.dropped';
    const DATABASE_COMMITTED = 'database.committed';
    const DATABASE_DOCUMENT_ADDED = 'database.document_added';
    const DATABASE_DOCUMENT_UPDATED = 'database.document_updated';
    const DATABASE_DOCUMENT_REMOVED = 'database.document_removed';
}