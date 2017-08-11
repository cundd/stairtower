<?php
declare(strict_types=1);

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Evenement\EventEmitterInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use function DI\object;

return call_user_func(
    function () {
        $ns = 'Cundd\\Stairtower\\';

        return [
            $ns . 'Formatter\\FormatterInterface'               => object($ns . 'Formatter\\Formatter'),
            $ns . 'Server\\Handler\\HandlerInterface'           => object($ns . 'Server\\Handler\\Handler'),
            $ns . 'Server\\ServerInterface'                     => object($ns . 'Server\\RestServer'),
            $ns . 'Server\\UriBuilderInterface'                 => object($ns . 'Server\\UriBuilder'),
            $ns . 'Server\\Cookie\\CookieParserInterface'       => object($ns . 'Server\\Cookie\\CookieParser'),
            $ns . 'Server\\Session\\SessionProviderInterface'   => object($ns . 'Server\\Session\\SessionProvider'),
            $ns . 'Server\\Dispatcher\\CoreDispatcher'          => object($ns . 'Server\\Dispatcher\\CoreDispatcher'),
            $ns . 'Asset\\AssetProviderInterface'               => object($ns . 'Asset\\AssetProvider'),
            $ns . 'DataAccess\\CoordinatorInterface'            => object($ns . 'DataAccess\\Coordinator'),
            $ns . 'DataAccess\\ObjectFinderInterface'           => object($ns . 'DataAccess\\ObjectFinder'),
            $ns . 'Serializer\\SerializerInterface'             => object($ns . 'Serializer\\JsonSerializer'),
            $ns . 'Serializer\\SerializerInterface'             => object($ns . 'Serializer\\JsonSerializer'),
            $ns . 'Filter\\FilterBuilderInterface'              => object($ns . 'Filter\\FilterBuilder'),
            $ns . 'Expand\\ExpandConfigurationBuilderInterface' => object($ns . 'Expand\\ExpandConfigurationBuilder'),
            $ns . 'Expand\\ExpandResolverInterface'             => object($ns . 'Expand\\ExpandResolver'),
            $ns . 'Expand\\ExpandResolverInterface'             => object($ns . 'Expand\\ExpandResolver'),
            EventEmitterInterface::class                        => object(\Evenement\EventEmitter::class),
            LoggerInterface::class                              => DI\factory(
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