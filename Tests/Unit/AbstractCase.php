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
		parent::setUp();


		$fixtureClass = substr(get_class($this), 0, -4);
		if (class_exists($fixtureClass)) {
			$this->fixture = $this->getDiContainer()->get($fixtureClass);
		}
	}

	protected function tearDown() {
		unset($this->fixture);
		unset($this->diContainer);

//		xdebug_debug_zval('this');
		var_dump(gc_enabled());
		parent::tearDown();

		echo $this->formatBytes(memory_get_peak_usage(TRUE)) . ' / ';
		echo $this->formatBytes(memory_get_usage(TRUE)) . PHP_EOL;

		gc_collect_cycles();

		echo $this->formatBytes(memory_get_peak_usage(TRUE)) . ' / ';
		echo $this->formatBytes(memory_get_usage(TRUE)) . PHP_EOL;

	}

	function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		// Uncomment one of the following alternatives
		$bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow));

		return round($bytes, $precision) . ' ' . $units[$pow];
	}

} 