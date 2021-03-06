<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Bootstrap;

use Cundd\Stairtower\ApplicationMode;
use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\DataAccess\CoordinatorInterface;
use Cundd\Stairtower\Router\ServerRequestFactory;
use Cundd\Stairtower\Server\Dispatcher\CoreDispatcherInterface;
use Cundd\Stairtower\Server\ServerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use React\Http\Response;

/**
 * Router bootstrapping for conventional servers
 */
class Router extends AbstractBootstrap
{
    /**
     * Current response instance
     *
     * @var Response
     */
    protected $response;

    /**
     * @var CoreDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var CoordinatorInterface
     */
    private $coordinator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param array $arguments
     */
    public function configure(array $arguments)
    {
        $serverMode = $this->getServerModeFromEnv();

        $configurationManager = ConfigurationManager::getSharedInstance();
        $configurationManager->setConfigurationForKeyPath('applicationMode', ApplicationMode::ROUTER);
        if ($serverMode === ServerInterface::SERVER_MODE_DEVELOPMENT) {
            $configurationManager->setConfigurationForKeyPath('logLevel', LogLevel::DEBUG);
        }

        $this->setDataPath((string)getenv(Constants::ENVIRONMENT_KEY_SERVER_DATA_PATH));

        // Instantiate the Core
        $bootstrap = new Core();
        $container = $bootstrap->getDiContainer();

        $this->coordinator = $container->get(CoordinatorInterface::class);
        $this->dispatcher = $container->get(CoreDispatcherInterface::class);
        $this->logger = $container->get(LoggerInterface::class);
    }

    protected function doExecute(array $arguments)
    {
        $request = ServerRequestFactory::fromGlobals();

        $this->dispatcher->dispatch($request)
            ->then([$this, 'sendResponse'])
            ->otherwise([$this, 'handleError']);

        $this->coordinator->commitDatabases();
    }

    public function handleError($error)
    {
        if ($error instanceof \Throwable) {
            $this->logger->error(
                sprintf(
                    'Caught exception #%d: %s',
                    $error->getCode(),
                    $error->getMessage(),
                    ['error' => $error]
                )
            );
        }
    }

    public function sendResponse(ResponseInterface $response)
    {
        if (headers_sent($file, $line)) {
            $this->logger->error(sprintf('Headers already sent by %s:%d', $file, $line));
        } else {
            header(
                sprintf(
                    'HTTP/%s %s %s',
                    $response->getProtocolVersion(),
                    $response->getStatusCode(),
                    $response->getReasonPhrase()
                ),
                true,
                $response->getStatusCode()
            );

            foreach ($response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
        }

        $body = $response->getBody();
        $body->rewind();
        echo $body;
    }

    /**
     * Returns the server mode according to the environment variables
     *
     * @return int
     */
    private function getServerModeFromEnv()
    {
        $serverModeName = getenv(Constants::ENVIRONMENT_KEY_SERVER_MODE);
        if ($serverModeName === 'dev') {
            return ServerInterface::SERVER_MODE_DEVELOPMENT;
        } elseif ($serverModeName === 'dev') {
            return ServerInterface::SERVER_MODE_TEST;
        }

        return ServerInterface::SERVER_MODE_NORMAL;
    }
}
