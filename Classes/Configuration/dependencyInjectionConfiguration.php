<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 12:56
 */
$persistentObjectStoreClassBase = 'Cundd\\PersistentObjectStore\\';
return array(
    $persistentObjectStoreClassBase . 'Formatter\\FormatterInterface'               => DI\object($persistentObjectStoreClassBase . 'Formatter\\Formatter'),
    $persistentObjectStoreClassBase . 'Server\\Handler\\HandlerInterface'           => DI\object($persistentObjectStoreClassBase . 'Server\\Handler\\Handler'),
    //$persistentObjectStoreClassBase . 'Server\\BodyParser\\BodyParserInterface' => DI\object($persistentObjectStoreClassBase . 'Server\\BodyParser\\JsonBodyParser'),
    $persistentObjectStoreClassBase . 'DataAccess\\CoordinatorInterface'            => DI\object($persistentObjectStoreClassBase . 'DataAccess\\Coordinator'),
    $persistentObjectStoreClassBase . 'DataAccess\\ObjectFinderInterface'           => DI\object($persistentObjectStoreClassBase . 'DataAccess\\ObjectFinder'),
    $persistentObjectStoreClassBase . 'Serializer\\SerializerInterface'             => DI\object($persistentObjectStoreClassBase . 'Serializer\\JsonSerializer'),
    $persistentObjectStoreClassBase . 'Serializer\\SerializerInterface'             => DI\object($persistentObjectStoreClassBase . 'Serializer\\JsonSerializer'),
    $persistentObjectStoreClassBase . 'Filter\\FilterBuilderInterface'              => DI\object($persistentObjectStoreClassBase . 'Filter\\FilterBuilder'),
    $persistentObjectStoreClassBase . 'Expand\\ExpandConfigurationBuilderInterface' => DI\object($persistentObjectStoreClassBase . 'Expand\\ExpandConfigurationBuilder'),
    $persistentObjectStoreClassBase . 'Expand\\ExpandResolverInterface'             => DI\object($persistentObjectStoreClassBase . 'Expand\\ExpandResolver'),
    'Evenement\\EventEmitterInterface'                                              => DI\object('Evenement\\EventEmitter'),
);