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
			$builder->setDefinitionCache(new \Doctrine\Common\Cache\ArrayCache());
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

		$this->tearDownXhprof();
		gc_collect_cycles();
	}

	/**
	 * Configure Xhprof
	 */
	protected function setUpXhprof() {
		if (!self::$didSetupXhprof && extension_loaded('xhprof')) {
			ini_set('xhprof.output_dir', '/Users/daniel/Sites/xhprof/runs');

			$XHPROF_ROOT = __DIR__ . '/../../xhprof-0.9.4/';
			include_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php';
			include_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php';

			xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

			self::$didSetupXhprof = TRUE;
			register_shutdown_function(array(__CLASS__, 'tearDownXhprof'));
		}
	}

	/**
	 * Write the Xhprof data
	 */
	static protected function tearDownXhprof() {
		if (extension_loaded('xhprof')) {
			$xhprofData = xhprof_disable();

			$xhprofRuns = new \XHProfRuns_Default();
			$runId      = $xhprofRuns->save_run($xhprofData, 'cundd_pos');

			echo PHP_EOL . 'http://localhost:8080/index.php?run=' . $runId . '&source=cundd_pos' . PHP_EOL;
		}
	}
} 