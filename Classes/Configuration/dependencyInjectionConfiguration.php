<?php
declare(strict_types=1);

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use React\EventLoop\Factory;

return call_user_func(
    function () {
        $ns = 'Cundd\\Stairtower\\';

        return [
            $ns . 'Formatter\\FormatterInterface'               => DI\object($ns . 'Formatter\\Formatter'),
            $ns . 'Server\\Handler\\HandlerInterface'           => DI\object($ns . 'Server\\Handler\\Handler'),
            $ns . 'Server\\UriBuilderInterface'                 => DI\object($ns . 'Server\\UriBuilder'),
            $ns . 'Server\\Cookie\\CookieParserInterface'       => DI\object($ns . 'Server\\Cookie\\CookieParser'),
            $ns . 'Server\\Session\\SessionProviderInterface'   => DI\object($ns . 'Server\\Session\\SessionProvider'),
            $ns . 'Asset\\AssetProviderInterface'               => DI\object($ns . 'Asset\\AssetProvider'),
            $ns . 'DataAccess\\CoordinatorInterface'            => DI\object($ns . 'DataAccess\\Coordinator'),
            $ns . 'DataAccess\\ObjectFinderInterface'           => DI\object($ns . 'DataAccess\\ObjectFinder'),
            $ns . 'Serializer\\SerializerInterface'             => DI\object($ns . 'Serializer\\JsonSerializer'),
            $ns . 'Serializer\\SerializerInterface'             => DI\object($ns . 'Serializer\\JsonSerializer'),
            $ns . 'Filter\\FilterBuilderInterface'              => DI\object($ns . 'Filter\\FilterBuilder'),
            $ns . 'Expand\\ExpandConfigurationBuilderInterface' => DI\object($ns . 'Expand\\ExpandConfigurationBuilder'),
            $ns . 'Expand\\ExpandResolverInterface'             => DI\object($ns . 'Expand\\ExpandResolver'),
            $ns . 'Expand\\ExpandResolverInterface'             => DI\object($ns . 'Expand\\ExpandResolver'),
            \Evenement\EventEmitterInterface::class             => DI\object(\Evenement\EventEmitter::class),
            \Psr\Log\LoggerInterface::class                     => DI\factory(
                function () {
                    $configurationManager = ConfigurationManager::getSharedInstance();
                    $logFileDirectory = $configurationManager->getConfigurationForKeyPath('logPath');
                    $logFilePath = $logFileDirectory . 'log-' . gmdate('Y-m-d') . '.log';
                    if (!file_exists($logFileDirectory)) {
                        mkdir($logFileDirectory, 0770, true);
                    }

                    $logLevel = $configurationManager->getConfigurationForKeyPath('logLevel');
                    $logger = new Logger('core');
                    $logger->pushHandler(new StreamHandler($logFilePath, $logLevel));
                    $logger->pushHandler(new StreamHandler(STDOUT, $logLevel));

                    return $logger;
                }
            ),
            \React\EventLoop\LoopInterface::class               => DI\factory(
                function () {
                    return Factory::create();
                }
            ),
        ];
    }
);