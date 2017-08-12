<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Server;

use Cundd\Stairtower\Configuration\ConfigurationManager;
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
        $phpBinPath = $configurationManager->getPhpBinaryPath();
        $serverBinPath = $configurationManager->getBinPath() . 'server';


        // Prepare the arguments
        $arguments = [];

        $dataPath = $this->getDataPath($input);
        if ($dataPath) {
            $arguments[] = '--data-path=' . $dataPath;
        }

        $arguments[] = '--ip=' . (string)$this->getServerIp($input);
        $arguments[] = '--port=' . (int)$this->getServerPort($input);


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
