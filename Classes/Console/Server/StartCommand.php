<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Server;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Console\Exception\InvalidArgumentsException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to start the server
 */
class StartCommand extends AbstractServerCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('server:start')
            ->setDescription('Start the server');

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Disable PHP's time limit
        set_time_limit(0);

        $configurationManager = ConfigurationManager::getSharedInstance();
        $serverBinPath = $configurationManager->getConfigurationForKeyPath('binPath') . 'server';
        $phpBinPath = $configurationManager->getConfigurationForKeyPath('phpBinPath');

        // Prepare the arguments
        $arguments = [];
        if ($input->hasArgument('data-path') && $input->getArgument('data-path')) {
            $dataPath = $input->getArgument('data-path');
            if ($dataPath === filter_var($dataPath, FILTER_SANITIZE_STRING)) {
                $arguments[] = '--data-path=' . $dataPath;
            } else {
                throw new InvalidArgumentsException('Invalid input for argument "data-path"', 1420812210);
            }
        }
        if ($input->hasArgument('ip') && $input->getArgument('ip')) {
            $ip = $input->getArgument('ip');
            if ($ip === filter_var($ip, FILTER_VALIDATE_URL) || $ip === filter_var($ip, FILTER_VALIDATE_IP)) {
                $arguments[] = '--ip=' . $ip;
            } else {
                throw new InvalidArgumentsException('Invalid input for argument "ip"', 1420812211);
            }
        }
        if ($input->hasArgument('port') && $input->getArgument('port')) {
            $port = $input->getArgument('port');
            if (is_numeric($port) && ctype_alnum($port)) {
                $arguments[] = '--port=' . $port;
            } else {
                throw new InvalidArgumentsException('Invalid input for argument "port"', 1420812212);
            }
        }
        if ($input->getOption('dev')) {
            $arguments[] = '--dev';
            $this->setDevMode(true);
        }

        $process = $this->processBuilder
            ->setPrefix([$phpBinPath, $serverBinPath])
            ->setArguments($arguments)
            ->setTimeout(null)
            ->getProcess();

        $this->startProcessAndWatch($process, $output);
    }
}
