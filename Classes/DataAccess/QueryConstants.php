<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\DataAccess;


interface QueryConstants
{
    /*
     * The query types
     */
    const SELECT = 'select';
    const DELETE = 'delete';
    const UPDATE = 'update';
    const INSERT = 'insert';
}