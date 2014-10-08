<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 31.08.14
 * Time: 16:16
 */

namespace Cundd\PersistentObjectStore;
use DI\ContainerBuilder;

/**
 * Abstract base class for tests
 *
 * @package Cundd\PersistentObjectStore
 */
class AbstractCase extends \PHPUnit_Framework_TestCase {
	protected $fixture;

	/**
	 * Dependency injection container
	 *
	 * @var \DI\Container
	 */
	protected $diContainer;

	/**
	 * Defines if Xhprof should be used
	 *
	 * @var bool
	 */
	static protected $useXhprof = FALSE;

	/**
	 * @var bool
	 */
	static protected $didSetupXhprof = FALSE;

	/**
	 * Returns the dependency injection container
	 *
	 * @return \DI\Container
	 */
	public function getDiContainer() {
		if (!$this->diContainer) {
			$builder = new ContainerBuilder();
//			$builder->setDefinitionCache(new \Doctrine\Common\Cache\ArrayCache());
			$builder->setDefinitionCache(new \Doctrine\Common\Cache\FilesystemCache(__DIR__ . '/../../var/Cache/'));
			$builder->addDefinitions(__DIR__ . '/../../Classes/Configuration/dependencyInjectionConfiguration.php');
			$this->diContainer = $builder->build();
//			$this->diContainer = ContainerBuilder::buildDevContainer();
		}
		return $this->diContainer;
	}



	protected function setUp() {
		$this->setUpXhprof();

		parent::setUp();
		$fixtureClass = substr(get_class($this), 0, -4);
		if (class_exists($fixtureClass)) {
			$this->fixture = $this->getDiContainer()->get($fixtureClass);
		}
	}

	protected function tearDown() {
//		unset($this->fixture);
//		unset($this->diContainer);
		gc_collect_cycles();
	}

	/**
	 * Checks if the congress member file exists
	 */
	protected function checkPersonFile() {
		$personsDataPath = __DIR__ . '/../Resources/people.json';
		if (!file_exists($personsDataPath)) {
			printf('Please unzip the file %s.zip to %s to run this tests', $personsDataPath, $personsDataPath);
			die(1);
		}
	}

	/**
	 * Configure Xhprof
	 */
	protected function setUpXhprof() {
		if (!self::$useXhprof) {
			return;
		}
		if (!self::$didSetupXhprof && extension_loaded('xhprof') && class_exists('XHProfRuns_Default')) {
			ini_set('xhprof.output_dir', '/Users/daniel/Sites/xhprof/runs');


			xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

			self::$didSetupXhprof = TRUE;
			register_shutdown_function(array(__CLASS__, 'tearDownXhprof'));
		}
	}

	/**
	 * Write the Xhprof data
	 */
	static public function tearDownXhprof() {
		if (!self::$useXhprof) {
			return;
		}

		if (self::$didSetupXhprof && extension_loaded('xhprof')) {
			$xhprofData = xhprof_disable();

//			$XHPROF_ROOT = __DIR__ . '/../../xhprof-0.9.4/';
//			require_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php';
//			require_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php';

			$xhprofRuns = new \XHProfRuns_Default();
			$runId      = $xhprofRuns->save_run($xhprofData, 'cundd_pos');

			echo PHP_EOL . 'http://localhost:8080/index.php?run=' . $runId . '&source=cundd_pos' . PHP_EOL;
		}
	}
}
