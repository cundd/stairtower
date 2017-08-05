<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 30.08.14
 * Time: 12:56
 */
use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use React\EventLoop\Factory;

$persistentObjectStoreClassBase = 'Cundd\\PersistentObjectStore\\';
return array(
    $persistentObjectStoreClassBase . 'Formatter\\FormatterInterface'               => DI\object($persistentObjectStoreClassBase . 'Formatter\\Formatter'),
    $persistentObjectStoreClassBase . 'Server\\Handler\\HandlerInterface'           => DI\object($persistentObjectStoreClassBase . 'Server\\Handler\\Handler'),
    $persistentObjectStoreClassBase . 'Server\\UriBuilderInterface'                 => DI\object($persistentObjectStoreClassBase . 'Server\\UriBuilder'),
    $persistentObjectStoreClassBase . 'Server\\Cookie\\CookieParserInterface'       => DI\object($persistentObjectStoreClassBase . 'Server\\Cookie\\CookieParser'),
    $persistentObjectStoreClassBase . 'Server\\Session\\SessionProviderInterface'   => DI\object($persistentObjectStoreClassBase . 'Server\\Session\\SessionProvider'),
    $persistentObjectStoreClassBase . 'Asset\\AssetProviderInterface'               => DI\object($persistentObjectStoreClassBase . 'Asset\\AssetProvider'),
    //$persistentObjectStoreClassBase . 'Server\\BodyParser\\BodyParserInterface' => DI\object($persistentObjectStoreClassBase . 'Server\\BodyParser\\JsonBodyParser'),
    $persistentObjectStoreClassBase . 'DataAccess\\CoordinatorInterface'            => DI\object($persistentObjectStoreClassBase . 'DataAccess\\Coordinator'),
    $persistentObjectStoreClassBase . 'DataAccess\\ObjectFinderInterface'           => DI\object($persistentObjectStoreClassBase . 'DataAccess\\ObjectFinder'),
    $persistentObjectStoreClassBase . 'Serializer\\SerializerInterface'             => DI\object($persistentObjectStoreClassBase . 'Serializer\\JsonSerializer'),
    $persistentObjectStoreClassBase . 'Serializer\\SerializerInterface'             => DI\object($persistentObjectStoreClassBase . 'Serializer\\JsonSerializer'),
    $persistentObjectStoreClassBase . 'Filter\\FilterBuilderInterface'              => DI\object($persistentObjectStoreClassBase . 'Filter\\FilterBuilder'),
    $persistentObjectStoreClassBase . 'Expand\\ExpandConfigurationBuilderInterface' => DI\object($persistentObjectStoreClassBase . 'Expand\\ExpandConfigurationBuilder'),
    $persistentObjectStoreClassBase . 'Expand\\ExpandResolverInterface'             => DI\object($persistentObjectStoreClassBase . 'Expand\\ExpandResolver'),
    $persistentObjectStoreClassBase . 'Expand\\ExpandResolverInterface'             => DI\object($persistentObjectStoreClassBase . 'Expand\\ExpandResolver'),
    // \Cundd\PersistentObjectStore\Server\Cookie\CookieParserInterface
    'Evenement\\EventEmitterInterface'                                              => DI\object('Evenement\\EventEmitter'),
    'Psr\\Log\\LoggerInterface'                                                     => DI\factory(function () {
        $configurationManager = ConfigurationManager::getSharedInstance();
        $logFileDirectory     = $configurationManager->getConfigurationForKeyPath('logPath');
        $logFilePath          = $logFileDirectory . 'log-' . gmdate('Y-m-d') . '.log';
        if (!file_exists($logFileDirectory)) {
            mkdir($logFileDirectory, 0770, true);
        }

        $logLevel = $configurationManager->getConfigurationForKeyPath('logLevel');
        $logger   = new Logger('core');
        $logger->pushHandler(new StreamHandler($logFilePath, $logLevel));
        $logger->pushHandler(new StreamHandler(STDOUT, $logLevel));

        return $logger;
    }),
    'React\\EventLoop\\LoopInterface'                                               => DI\factory(function () {
        return Factory::create();
    })
);