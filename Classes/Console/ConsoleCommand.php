<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore\Console;


use Cundd\PersistentObjectStore\Constants;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to run an interactive session
 */
class ConsoleCommand extends Command
{
    /**
     * Document Access Coordinator
     *
     * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
     * @Inject
     */
    protected $coordinator;

    /**
     * Commands to terminate the console
     *
     * @var array
     */
    protected $exitCommands = ['quit', 'exit', '\\q'];

    /**
     * @var array
     */
    protected $aliases = [
        'll' => 'list',
        'h'  => 'help',
    ];

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('console')
            ->setDescription('Run an interactive session');
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
        $output->write(Constants::MESSAGE_CLI_WELCOME);
        $output->write('> ');
        while ($line = fgets(STDIN, 1024 * 5)) {
            $line = trim($line);

            if (in_array($line, $this->exitCommands)) {
                $output->writeln('Goodbye');
                break;
            }

            $commandParts = explode(' ', $line);
            $subCommand = array_shift($commandParts);
            $this->executeSub($subCommand, $commandParts, $output);

            $output->write('> ');
        }
    }


    /**
     * Executes a subcommand
     *
     * @param string          $subcommand
     * @param array           $arguments
     * @param OutputInterface $output
     */
    public function executeSub($subcommand, array $arguments, OutputInterface $output)
    {
        $subcommandMethod = $this->getSubCommandMethod($subcommand);
        try {
            if (!$subcommandMethod) {
                throw new \InvalidArgumentException(sprintf('Undefined sub command "%s"', $subcommand), 1412525230);
            }
            $this->$subcommandMethod($output, $arguments);
        } catch (\Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
        }
    }

    /**
     * Returns a list of available sub commands
     *
     * @return string[]
     */
    private function getAvailableSubCommands()
    {
        return array_filter(
            get_class_methods(__CLASS__),
            function ($method) {
                return substr($method, -7) === 'Command';
            }
        );
    }

    /**
     * @param OutputInterface $output
     * @param array           $arguments
     */
    public function createCommand(OutputInterface $output, array $arguments)
    {
        $databaseIdentifier = array_shift($arguments);
        $options = $arguments;
        if (!$databaseIdentifier) {
            throw new \InvalidArgumentException('Missing database identifier argument', 1412524227);
        }
        $database = $this->coordinator->createDatabase($databaseIdentifier, $options);
        if ($database) {
            $output->writeln(sprintf('<info>Created database %s</info>', $databaseIdentifier));
        }
    }

    /**
     * @param OutputInterface $output
     * @param array           $arguments
     */
    public function dropCommand(OutputInterface $output, array $arguments)
    {
        $databaseIdentifier = array_shift($arguments);
        if (!$databaseIdentifier) {
            throw new \InvalidArgumentException('Missing database identifier argument', 1412524227);
        }
        $this->coordinator->dropDatabase($databaseIdentifier);
        $output->writeln(sprintf('<info>Dropped database %s</info>', $databaseIdentifier));
    }

    /**
     * @param OutputInterface $output
     */
    public function listCommand(OutputInterface $output)
    {
        $databases = $this->coordinator->listDatabases();
        if ($databases) {
            $output->writeln('<info>Databases:</info>');
            foreach ($databases as $databaseIdentifier) {
                $output->writeln($databaseIdentifier);
            }
        } else {
            $output->writeln('<info>No databases found</info>');
        }
    }

    /**
     * @param OutputInterface $output
     */
    public function helpCommand(OutputInterface $output)
    {
        $availableSubCommands = $this->getAvailableSubCommands();
        $output->writeln("<info>Available commands:</info>");
        foreach ($availableSubCommands as $subCommandMethod) {
            $subCommand = substr($subCommandMethod, 0, -7);
            $output->writeln($subCommand);
        }
    }

    /**
     * @param string $subcommand
     * @return string
     */
    private function getSubCommandMethod($subcommand)
    {
        $availableSubCommands = $this->getAvailableSubCommands();
        if (in_array($subcommand . 'Command', $availableSubCommands)) {
            return $subcommand . 'Command';
        }

        if (!isset($this->aliases[$subcommand])) {
            return '';
        }

        $aliasCommand = $this->aliases[$subcommand];
        if (in_array($aliasCommand . 'Command', $availableSubCommands)) {
            return $aliasCommand . 'Command';
        }

        return '';
    }
}
