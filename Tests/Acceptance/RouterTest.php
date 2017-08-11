<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Tests\Acceptance;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Symfony\Component\Process\Process;

/**
 * Test for REST commands
 */
class RouterTest extends AbstractAcceptanceCase
{
    protected $numberOfDocumentsToCreate = 10;

    protected function startServer(int $autoShutdownTime = 7): Process
    {
        $configurationManager = ConfigurationManager::getSharedInstance();
        $routerPath = $configurationManager->getBinPath() . 'router.php';
        $phpBinPath = $configurationManager->getPhpBinaryPath();
        $documentRoot = $configurationManager->getBasePath();

        $arguments = ['-S', $this->getUriForTestServer(), $routerPath];
        $process = $this->getProcessBuilder()
            ->setPrefix(['exec', $phpBinPath])
            ->setArguments($arguments)
            ->setTimeout($autoShutdownTime + 1)
            ->setWorkingDirectory($documentRoot)
            ->getProcess();

        $process->start();

        // Wait for the server to boot
        usleep(100 * 1000);

        return $process;
    }
}
