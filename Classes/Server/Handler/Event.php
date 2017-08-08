<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Server\Handler;


interface Event
{
    const DATABASE_CREATED = 'database.created';
    const DATABASE_DELETED = 'database.deleted';

    const DOCUMENT_CREATED = 'document.created';
    const DOCUMENT_UPDATED = 'document.updated';
    const DOCUMENT_DELETED = 'document.deleted';
}