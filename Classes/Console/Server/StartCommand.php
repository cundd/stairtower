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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Console command to start the server
 *
 * @package Cundd\PersistentObjectStore\Console
 */
class StartCommand extends Command
{
    /**
     * @var \Symfony\Component\Process\ProcessBuilder
     * @Inject
     */
    protected $processBuilder;

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('server:start')
            ->setDescription('Start the server')
            ->addArgument(
                'ip',
                InputArgument::OPTIONAL,
                'Server IP address'
            )
            ->addArgument(
                'port',
                InputArgument::OPTIONAL,
                'Server port'
            )
            ->addArgument(
                'data-path',
                InputArgument::OPTIONAL,
                'Directory path where the data is stored'
            )
            ->addOption(
                'dev',
                null,
                InputOption::VALUE_NONE,
                'Start the server in development mode'
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
        $serverBinPath        = $configurationManager->getConfigurationForKeyPath('binPath') . 'server';
        $phpBinPath           = $configurationManager->getConfigurationForKeyPath('phpBinPath');

        // Prepare the arguments
        $arguments = array();
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
        }

        $process = $this->processBuilder
            ->setPrefix(array($phpBinPath, $serverBinPath))
            ->setArguments($arguments)
            ->setTimeout(null)
            ->getProcess();

        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $output->writeln(sprintf('<info>Start server using command %s</info>', $process->getCommandLine()));
        }

        $exitedSuccessfully = false;
        while (!$exitedSuccessfully) {
            $process->start();
            $process->wait(function ($type, $buffer) use ($output) {
                if (Process::ERR === $type) {
                    $output->writeln(sprintf('<error>%s</error>', $buffer));
                } else {
                    $output->writeln($buffer);
                }
            });

            $exitedSuccessfully = $process->getExitCode() === 0;
            if ($exitedSuccessfully) {
                $output->writeln('<info>Terminated</info>');
            } else {
                $output->writeln('<error>Crashed</error>');
                $output->writeln('<info>Will restart the server</info>');
            }
        }
    }
}