<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 12:56
 */
use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Utility\GeneralUtility;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$persistentObjectStoreClassBase = 'Cundd\\PersistentObjectStore\\';
return array(
    $persistentObjectStoreClassBase . 'Formatter\\FormatterInterface'     => DI\object($persistentObjectStoreClassBase . 'Formatter\\Formatter'),
    $persistentObjectStoreClassBase . 'Server\\Handler\\HandlerInterface' => DI\object($persistentObjectStoreClassBase . 'Server\\Handler\\Handler'),
    //$persistentObjectStoreClassBase . 'Server\\BodyParser\\BodyParserInterface' => DI\object($persistentObjectStoreClassBase . 'Server\\BodyParser\\JsonBodyParser'),
    $persistentObjectStoreClassBase . 'DataAccess\\CoordinatorInterface'  => DI\object($persistentObjectStoreClassBase . 'DataAccess\\Coordinator'),
    $persistentObjectStoreClassBase . 'DataAccess\\ObjectFinderInterface' => DI\object($persistentObjectStoreClassBase . 'DataAccess\\ObjectFinder'),
    $persistentObjectStoreClassBase . 'Serializer\\SerializerInterface'   => DI\object($persistentObjectStoreClassBase . 'Serializer\\JsonSerializer'),
    $persistentObjectStoreClassBase . 'Serializer\\SerializerInterface'   => DI\object($persistentObjectStoreClassBase . 'Serializer\\JsonSerializer'),
    $persistentObjectStoreClassBase . 'Filter\\FilterBuilderInterface'    => DI\object($persistentObjectStoreClassBase . 'Filter\\FilterBuilder'),

    'Evenement\\EventEmitterInterface'                                    => DI\object('Evenement\\EventEmitter'),
    'Psr\\Log\\LoggerInterface' => DI\factory(function () {
        $configurationManager = ConfigurationManager::getSharedInstance();
        $logFileDirectory     = $configurationManager->getConfigurationForKeyPath('logPath');
//        $logFilePath      = $logFileDirectory . 'log-' . getmypid() . '.log';
        $logFilePath = $logFileDirectory . 'log-' . gmdate('Y-m-d') . '.log';
        if (!file_exists($logFileDirectory)) {
            GeneralUtility::createDirectoryRecursive($logFileDirectory, true);
        }

        $logLevel = $configurationManager->getConfigurationForKeyPath('logLevel');
        $logger      = new Logger('stairtower');

        $logger->pushHandler(new StreamHandler($logFilePath, $logLevel));
        $logger->pushHandler(new StreamHandler(STDOUT, $logLevel));

        return $logger;
    }),
);