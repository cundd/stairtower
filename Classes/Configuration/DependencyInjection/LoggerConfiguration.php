<?php
declare(strict_types=1);

use Cundd\Stairtower\ApplicationMode;
use Cundd\Stairtower\Configuration\ConfigurationManager;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

return call_user_func(
    function () {
        return [
            LoggerInterface::class => DI\factory(
                function () {
                    $configurationManager = ConfigurationManager::getSharedInstance();
                    $logFileDirectory = $configurationManager->getLogPath();
                    $logFilePath = $logFileDirectory . 'log-' . gmdate('Y-m-d') . '.log';
                    if (!file_exists($logFileDirectory)) {
                        mkdir($logFileDirectory, 0770, true);
                    }

                    $logLevel = $configurationManager->getLogLevel();

                    $logger = new Logger('core');
                    $logger->pushHandler(new StreamHandler($logFilePath, $logLevel));
                    if ($configurationManager->getApplicationMode() === ApplicationMode::ROUTER) {
                        $logger->pushHandler(new StreamHandler(STDERR, $logLevel));
                    } else {
                        $logger->pushHandler(new StreamHandler(STDOUT, $logLevel));
                    }

                    return $logger;
                }
            ),
        ];
    }
);
