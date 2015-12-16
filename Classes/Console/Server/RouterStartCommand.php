<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console\Server;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Console\Exception\InvalidArgumentsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Console command to start PHP's built-in server
 *
 * @package Cundd\PersistentObjectStore\Console
 */
class RouterStartCommand extends AbstractServerCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('server:router-start')
            ->setDescription('Start PHP\'s built-in server');

        parent::configure();
        $this
            ->addArgument(
                'document-root',
                InputArgument::OPTIONAL,
                'Document root for the server'
            );
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
        $phpBinPath = $configurationManager->getConfigurationForKeyPath('phpBinPath');


        // Prepare the environment variables
        $environmentVariables = array();
        if ($input->hasArgument('data-path') && $input->getArgument('data-path')) {
            $dataPath = $input->getArgument('data-path');
            if ($dataPath === filter_var($dataPath, FILTER_SANITIZE_STRING)) {
                $environmentVariables['STAIRTOWER_SERVER_DATA_PATH'] = $dataPath;
            } else {
                throw new InvalidArgumentsException('Invalid input for argument "data-path"', 1420812210);
            }
        }
        if ($input->getOption('dev')) {
            $environmentVariables['STAIRTOWER_SERVER_MODE'] = 'dev';
            $this->setDevMode(true);
        }


        // Prepare the document root
        if ($input->hasArgument('document-root') && $input->getArgument('document-root')) {
            $documentRoot = $input->getArgument('document-root');
            if ($documentRoot !== filter_var($documentRoot, FILTER_SANITIZE_STRING)) {
                throw new InvalidArgumentsException('Invalid input for argument "document-root"', 1420812213);
            }
        } else {
            $documentRoot = $configurationManager->getConfigurationForKeyPath('basePath');
        }


        // Prepare the arguments
        $address = '127.0.0.1';
        if ($input->hasArgument('ip') && $input->getArgument('ip')) {
            $ip = $input->getArgument('ip');
            if ($ip === filter_var($ip, FILTER_VALIDATE_URL) || $ip === filter_var($ip, FILTER_VALIDATE_IP)) {
                $address = $ip;
            } else {
                throw new InvalidArgumentsException('Invalid input for argument "ip"', 1420812211);
            }
        }
        if ($input->hasArgument('port') && $input->getArgument('port')) {
            $port = $input->getArgument('port');
            if (is_numeric($port) && ctype_alnum($port)) {
                $address .= ':'.$port;
            } else {
                throw new InvalidArgumentsException('Invalid input for argument "port"', 1420812212);
            }
        } else {
            $address .= ':1338';
        }

        $routerPath = $configurationManager->getConfigurationForKeyPath('binPath').'router.php';
        $arguments = ['-S', $address, $routerPath];
        $process = $this->processBuilder
            ->setPrefix(array('exec', $phpBinPath))
            ->setArguments($arguments)
            ->setTimeout(null)
            ->setWorkingDirectory($documentRoot)
            ->addEnvironmentVariables($environmentVariables)
            ->getProcess();

        $this->startProcessAndWatch($process, $output);
    }
}
