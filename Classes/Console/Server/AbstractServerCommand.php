<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Server;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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

            $exitedSuccessfully = $process->getExitCode() === 0;
            if ($exitedSuccessfully) {
                $output->writeln('<info>Terminated</info>');
            } elseif ($automaticallyRestart) {
                $output->writeln('<error>Crashed</error>');
                $output->writeln('<info>Will restart the server</info>');
            } else {
                $output->writeln('<error>Crashed</error>');
            }
        } while (!$exitedSuccessfully && $automaticallyRestart);
    }

    /**
     * Returns if the server should be automatically restarted after crash
     *
     * @return bool
     */
    protected function automaticallyRestart()
    {
        return !$this->isDevMode();
    }

    /**
     * Returns if the server is started in development mode
     *
     * @return boolean
     */
    protected function isDevMode()
    {
        return $this->devMode;
    }

    /**
     * Sets if the server is started in development mode
     *
     * @param boolean $devMode
     */
    protected function setDevMode($devMode)
    {
        $this->devMode = !!$devMode;
    }
}
