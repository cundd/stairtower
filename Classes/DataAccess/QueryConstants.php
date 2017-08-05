<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 17:29
 */

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