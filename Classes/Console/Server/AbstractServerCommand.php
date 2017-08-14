<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Server;

use Cundd\Stairtower\Console\Exception\InvalidArgumentsException;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Server\Exception\BootException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Abstract console command to start the server
 */
abstract class AbstractServerCommand extends Command
{
    /**
     * @var \Symfony\Component\Process\ProcessBuilder
     * @Inject
     */
    protected $processBuilder;

    /**
     * Defines if the server is started in development mode
     *
     * @var bool
     */
    private $devMode = false;

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
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
     * @param Process         $process
     * @param OutputInterface $output
     */
    protected function startProcessAndWatch(Process $process, OutputInterface $output)
    {
        if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
            $output->writeln(sprintf('<info>Start server using command %s</info>', $process->getCommandLine()));
        }

        $automaticallyRestart = $this->automaticallyRestart();
        do {
            $process->start();
            $process->wait(
                function ($type, $buffer) use ($output) {
                    if (Process::ERR === $type) {
                        $output->writeln(sprintf('<error>%s</error>', $buffer));
                    } else {
                        $output->writeln($buffer);
                    }
                }
            );

            if (0 === $process->getExitCode()) {
                $output->writeln('<info>Terminated</info>');

                return;
            }

            if (!$automaticallyRestart) {
                $output->writeln('<error>Crashed</error>');

                return;
            }

            if ($this->isBootError($process)) {
                $output->writeln('<error>Boot error will NOT restart the server</error>');

                return;
            }

            $output->writeln('<error>Crashed</error>');
            $output->writeln('<info>Will restart the server</info>');
        } while (true);
    }

    /**
     * Returns if the server should be automatically restarted after crash
     *
     * @return bool
     */
    protected function automaticallyRestart(): bool
    {
        return !$this->isDevMode();
    }

    /**
     * Returns if the server is started in development mode
     *
     * @return boolean
     */
    protected function isDevMode(): bool
    {
        return $this->devMode;
    }

    /**
     * Sets if the server is started in development mode
     *
     * @param boolean $devMode
     */
    protected function setDevMode(bool $devMode)
    {
        $this->devMode = !!$devMode;
    }

    /**
     * Returns the server port to listen
     *
     * If the argument "port" exists if will be read, otherwise the default port is used
     *
     * @param InputInterface $input
     * @return int
     * @throws InvalidArgumentsException if the given port is not an integer
     */
    protected function getServerPort(InputInterface $input): int
    {
        if ($input->hasArgument('port') && $input->getArgument('port')) {
            $port = $input->getArgument('port');
            if (is_numeric($port) && ctype_alnum($port)) {
                return (int)$port;
            } else {
                throw new InvalidArgumentsException('Invalid input for argument "port"', 1420812212);
            }
        }

        return Constants::SERVER_DEFAULT_PORT;
    }

    /**
     * Returns the server IP to listen
     *
     * If the argument "ip" exists if will be read, otherwise the default IP is used
     *
     * @param InputInterface $input
     * @return string
     * @throws InvalidArgumentsException if the given IP is neither a valid URL nor a IP
     */
    protected function getServerIp(InputInterface $input): string
    {
        if ($input->hasArgument('ip') && $input->getArgument('ip')) {
            $ip = $input->getArgument('ip');
            if ($ip === filter_var($ip, FILTER_VALIDATE_URL) || $ip === filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }

            throw new InvalidArgumentsException('Invalid input for argument "ip"', 1420812211);
        }

        return Constants::SERVER_DEFAULT_IP;
    }

    /**
     * Returns the value of the "data-path" argument, or null if it is not given
     *
     * @param InputInterface $input
     * @return string|null
     * @throws InvalidArgumentsException if the given path is not a valid string
     */
    protected function getDataPath(InputInterface $input): ?string
    {
        if ($input->hasArgument('data-path') && $input->getArgument('data-path')) {
            $dataPath = $input->getArgument('data-path');
            if ($dataPath === filter_var($dataPath, FILTER_SANITIZE_STRING)) {
                return $dataPath;
            }

            throw new InvalidArgumentsException('Invalid input for argument "data-path"', 1420812210);
        }

        return null;
    }

    /**
     * @param Process $process
     * @return bool
     */
    protected function isBootError(Process $process): bool
    {
        return $process->getExitCode() === BootException::EXIT_CODE
            || strpos($process->getErrorOutput(), 'Failed to listen on') !== false;
    }
}
