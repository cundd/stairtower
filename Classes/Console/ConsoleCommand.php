<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console;


use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to run an interactive session
 *
 * @package Cundd\PersistentObjectStore\Console
 */
class ConsoleCommand extends Command {
	const MESSAGE_WELCOME = <<<WELCOME


                        /\
                       /  \
                      /____\
      __________      |    |
    /__________/\     |[]_ |
   /__________/()\    |   -|_
  /__________/    \   |    |
  | [] [] [] | [] |  _|    |
  |   ___    |    |   |–_  |
  |   |_| [] | [] |   |  –_|

         STAIRTOWER
   PERSISTENT OBJECT STORE
    a home for your data

WELCOME;

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
	protected $exitCommands = array('quit', 'exit', '\\q');

	/**
	 * Configure the command
	 */
	protected function configure() {
		$this
			->setName('console')
			->setDescription('Run an interactive session')
//			->addArgument(
//				'name',
//				InputArgument::OPTIONAL,
//				'Who do you want to greet?'
//			)
//			->addOption(
//				'yell',
//				null,
//				InputOption::VALUE_NONE,
//				'If set, the task will yell in uppercase letters'
//			)
		;
	}

	/**
	 * Execute the command
	 *
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 * @return int|null|void
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$output->write(self::MESSAGE_WELCOME);
		$output->write('> ');
		while ($line = fgets(STDIN, 1024 * 5)) {
			$line = trim($line);

			if (in_array($line, $this->exitCommands)) {
				$output->writeln('Goodbye');
				break;
			}

			$commandParts = explode(' ', $line);
			$subCommand   = array_shift($commandParts);
			$this->executeSubCommand($subCommand, $commandParts, $output);


			$output->write('> ');
			#readline_add_history($line);
		}
		#stream_get_line( resource $handle , int $length [, string $ending ] )
//		$name = $input->getArgument('name');
//		if ($name) {
//			$text = 'Hello ' . $name;
//		} else {
//			$text = 'Hello';
//		}
//
//		if ($input->getOption('yell')) {
//			$text = strtoupper($text);
//		}
//
//		$output->writeln($text);
	}


	/**
	 * Executes a subcommand
	 *
	 * @param string          $subcommand
	 * @param array           $arguments
	 * @param OutputInterface $output
	 */
	public function executeSubCommand($subcommand, $arguments, OutputInterface $output) {
		try {
			switch ($subcommand) {
				case 'create':
					$databaseIdentifier = array_shift($arguments);
					$options            = $arguments;
					if (!$databaseIdentifier) throw new \InvalidArgumentException('Missing database identifier argument', 1412524227);
					$database = $this->coordinator->createDatabase($databaseIdentifier, $options);
					if ($database) {
						$output->writeln(sprintf('<info>Created database %s</info>', $databaseIdentifier));
					}
					break;

				case 'drop':
					$databaseIdentifier = array_shift($arguments);
					if (!$databaseIdentifier) throw new \InvalidArgumentException('Missing database identifier argument', 1412524227);
					$this->coordinator->dropDatabase($databaseIdentifier);
					$output->writeln(sprintf('<info>Dropped database %s</info>', $databaseIdentifier));
					break;

				case 'list':
				case 'll':
					$databases = $this->coordinator->listDatabases();
					if ($databases) {
						$output->writeln('<info>Databases:</info>');
						foreach ($databases as $databaseIdentifier) {
							$output->writeln($databaseIdentifier);
						}
					} else {
						$output->writeln('<info>No databases found</info>');
					}
					break;

				default:
					throw new \InvalidArgumentException(sprintf('Undefined sub command "%s"', $subcommand), 1412525230);
			}
		} catch (\Exception $exception) {
			$output->writeln('<error>' . $exception->getMessage() . '</error>');
		}
	}
} 