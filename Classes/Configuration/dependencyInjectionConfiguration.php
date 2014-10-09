<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 12:56
 */
$persistentObjectStoreClassBase = 'Cundd\\PersistentObjectStore\\';

return array(
	$persistentObjectStoreClassBase . 'Formatter\\FormatterInterface' => DI\object($persistentObjectStoreClassBase . 'Formatter\\Formatter'),
	$persistentObjectStoreClassBase . 'DataAccess\\CoordinatorInterface' => DI\object($persistentObjectStoreClassBase . 'DataAccess\\Coordinator'),
	$persistentObjectStoreClassBase . 'DataAccess\\ObjectFinderInterface' => DI\object($persistentObjectStoreClassBase . 'DataAccess\\ObjectFinder'),
	$persistentObjectStoreClassBase . 'Serializer\\SerializerInterface' => DI\object($persistentObjectStoreClassBase . 'Serializer\\JsonSerializer'),
);