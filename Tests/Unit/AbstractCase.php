<?php
declare(strict_types=1);

namespace Cundd\PersistentObjectStore;

use Cundd\PersistentObjectStore\Configuration\ConfigurationManager;
use Cundd\PersistentObjectStore\Memory\Manager;
use DI\ContainerBuilder;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

/**
 * Abstract base class for tests
 */
class AbstractCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Defines if Xhprof should be used
     *
     * @var bool
     */
    static protected $useXhprof = true;
    /**
     * @var bool
     */
    static protected $didSetupXhprof = false;

    protected $fixture;

    /**
     * Dependency injection container
     *
     * @var \DI\Container
     */
    protected $diContainer;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
//		Coordinator::flushObjectStore();
    }

    /**
     * Write the Xhprof data
     */
    static public function tearDownXhprof()
    {
        if (!self::$useXhprof) {
            return;
        }

        if (self::$didSetupXhprof && extension_loaded('xhprof')) {
            $xhprofData = xhprof_disable();

//			$XHPROF_ROOT = __DIR__ . '/../../xhprof-0.9.4/';
//			require_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_lib.php';
//			require_once $XHPROF_ROOT . '/xhprof_lib/utils/xhprof_runs.php';

            $xhprofRuns = new \XHProfRuns_Default();
            $runId = $xhprofRuns->save_run($xhprofData, 'cundd_pos');

            echo PHP_EOL . 'http://localhost:8080/index.php?run=' . $runId . '&source=cundd_pos' . PHP_EOL;
        }
    }

    protected function setUp()
    {
        if (class_exists('Cundd\PersistentObjectStore\Memory\Manager')) {
            Manager::freeAll();
        }

        $this->setUpXhprof();

        parent::setUp();
        $fixtureClass = substr(get_class($this), 0, -4);
        if (class_exists($fixtureClass)) {
            $this->fixture = $this->getDiContainer()->get($fixtureClass);
        }
    }

    /**
     * Configure Xhprof
     */
    protected function setUpXhprof()
    {
        if (!self::$useXhprof) {
            return;
        }
        if (!self::$didSetupXhprof && extension_loaded('xhprof') && class_exists('XHProfRuns_Default')) {
            ini_set(
                'xhprof.output_dir',
                ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('tempPath')
            );


            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);

            self::$didSetupXhprof = true;
            register_shutdown_function([__CLASS__, 'tearDownXhprof']);

            echo PHP_EOL . 'Manually start xhprof server if needed:' . PHP_EOL;
            printf(
                'php -S 127.0.0.1:8080 -d xhprof.output_dir="%s" -t path/to/xhprof_html' . PHP_EOL,
                ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('tempPath')
            );

        }
    }

    /**
     * Returns the dependency injection container
     *
     * @return \DI\Container
     */
    public function getDiContainer()
    {
        if (!$this->diContainer) {
            $builder = new ContainerBuilder();
            $builder->useAnnotations(true);
            $builder->setDefinitionCache(new \Doctrine\Common\Cache\ArrayCache());
//            $builder->setDefinitionCache(new FilesystemCache(__DIR__ . '/../../var/Cache/'));
            $builder->addDefinitions(__DIR__ . '/../../Classes/Configuration/dependencyInjectionConfiguration.php');
            $this->diContainer = $builder->build();
//			$this->diContainer = ContainerBuilder::buildDevContainer();

            $this->diContainer->get('Cundd\\PersistentObjectStore\\Event\\SharedEventEmitter');

            $logger = new Logger('core');
            $logger->pushHandler(new NullHandler());
            $this->diContainer->set('Psr\\Log\\LoggerInterface', $logger);
        }

        return $this->diContainer;
    }

    protected function tearDown()
    {
//		unset($this->fixture);
//		unset($this->diContainer);
        if (class_exists('Cundd\PersistentObjectStore\Memory\Manager')) {
//			MemoryManager::freeObjectsByTag(Coordinator::MEMORY_MANAGER_TAG);
            Manager::freeAll();
        }
        gc_collect_cycles();
    }

    /**
     * Checks if the congress member file exists
     *
     * @return string
     */
    protected function checkPersonFile()
    {
        $personsDataPath = __DIR__ . '/../Resources/people.json';
        if (!file_exists($personsDataPath)) {
            printf('Please unzip the file %s.zip to %s to run this tests', $personsDataPath, $personsDataPath);
            die(1);
        }

        return $personsDataPath;
    }
}
