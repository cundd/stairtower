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

		gc_collect_cycles();
	}
} 