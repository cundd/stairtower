<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract command
 *
 * @package Cundd\PersistentObjectStore\Console
 */
abstract class AbstractCommand extends Command {
	/**
	 * Data Access Coordinator
	 *
	 * @var \Cundd\PersistentObjectStore\DataAccess\CoordinatorInterface
	 * @Inject
	 */
	protected $coordinator;

	/**
	 * Serializer instance
	 *
	 * @var \Cundd\PersistentObjectStore\Serializer\DataInstanceSerializer
	 * @Inject
	 */
	protected $serializer;
}