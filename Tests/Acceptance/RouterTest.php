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
    protected $numberOfWelcomeTests = 10;

    protected function configureServerProcess(int $autoShutdownTime = 7): Process
    {
        $configurationManager = ConfigurationManager::getSharedInstance();
        $routerPath = $configurationManager->getBinPath() . 'router.php';
        $phpBinPath = $configurationManager->getPhpBinaryPath();
        $documentRoot = $configurationManager->getBasePath();

        $arguments = ['-S', $this->getUriForTestServer(), $routerPath];

        return $this->getProcessBuilder()
            ->setPrefix(['exec', $phpBinPath])
            ->setArguments($arguments)
            ->setTimeout($autoShutdownTime + 1)
            ->setWorkingDirectory($documentRoot)
            ->getProcess();
    }
}
