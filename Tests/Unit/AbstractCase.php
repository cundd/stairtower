<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Unit;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Event\SharedEventEmitter;
use Cundd\Stairtower\Memory\Manager;
use DI\ContainerBuilder;
use Doctrine\Common\Cache\ArrayCache;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Abstract base class for tests
 */
class AbstractCase extends TestCase
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
    }


    protected function setUp()
    {
        $this->memoryManagerFree();
        $this->setUpXhprof();

        parent::setUp();

        // Automatically instantiate the fixture
        $fixtureClass = 'Cundd\\Stairtower' . substr(get_class($this), 27, -4);
        if (class_exists($fixtureClass)) {
            $this->fixture = $this->getDiContainer()->get($fixtureClass);
        }
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
            if (class_exists('XHProfRuns_Default')) {
                $xhprofRuns = new \XHProfRuns_Default();
                $runId = $xhprofRuns->save_run($xhprofData, 'cundd_pos');

                echo PHP_EOL . 'http://localhost:8080/index.php?run=' . $runId . '&source=cundd_pos' . PHP_EOL;
            }
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


            xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY, []);

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
            $builder->setDefinitionCache(new ArrayCache());
            $builder->addDefinitions(
                __DIR__ . '/../../Classes/Configuration/DependencyInjection/MainConfiguration.php'
            );
            $builder->addDefinitions(
                __DIR__ . '/../../Classes/Configuration/DependencyInjection/LoggerConfiguration.php'
            );
            $this->diContainer = $builder->build();

            $this->diContainer->get(SharedEventEmitter::class);

            $logger = new Logger('core');
            $logger->pushHandler(new NullHandler());
            $this->diContainer->set(LoggerInterface::class, $logger);
        }

        return $this->diContainer;
    }

    protected function tearDown()
    {
        $this->memoryManagerFree();
        gc_collect_cycles();
        parent::tearDown();
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

    protected function memoryManagerFree(): void
    {
        if (class_exists(Manager::class)) {
            Manager::freeAll();
        }
    }

    /**
     * @param string $original
     * @param string $alias
     */
    protected function makeClassAliasIfNotExists(string $original, string $alias)
    {
        if (!class_exists($alias)) {
            class_alias($original, $alias);
        }
    }
}
