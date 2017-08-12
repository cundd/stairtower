<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Server;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\Console\Exception\InvalidArgumentsException;
use Cundd\Stairtower\Constants;
use Cundd\Stairtower\Server\OutputWriterTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to start PHP's built-in server
 */
class RouterStartCommand extends AbstractServerCommand
{
    use OutputWriterTrait;

    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('server:router-start')
            ->setDescription('Start PHP\'s built-in server');

        parent::configure();
        $this
            ->addArgument(
                'document-root',
                InputArgument::OPTIONAL,
                'Document root for the server'
            );
    }

    /**
     * Execute the command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Disable PHP's time limit
        set_time_limit(0);

        $configurationManager = ConfigurationManager::getSharedInstance();
        $phpBinPath = $configurationManager->getPhpBinaryPath();
        $routerPath = $configurationManager->getBinPath() . 'router.php';


        // Prepare the environment variables
        $environmentVariables = [];

        $dataPath = $this->getDataPath($input);
        if ($dataPath) {
            $environmentVariables['STAIRTOWER_SERVER_DATA_PATH'] = $dataPath;
        }

        if ($input->getOption('dev')) {
            $environmentVariables['STAIRTOWER_SERVER_MODE'] = 'dev';
            $this->setDevMode(true);
        }

        $documentRoot = $this->getDocumentRoot($input);

        $arguments = [
            '-S',
            $this->getServerUri($input),
            $routerPath,
        ];
        $process = $this->processBuilder
            ->setPrefix(['exec', $phpBinPath])
            ->setArguments($arguments)
            ->setTimeout(null)
            ->setWorkingDirectory($documentRoot)
            ->addEnvironmentVariables($environmentVariables)
            ->getProcess();

        $this->writeln(Constants::MESSAGE_CLI_WELCOME . PHP_EOL);
        $this->writeln('Start listening on %s:%s', $this->getServerIp($input), $this->getServerPort($input));

        $this->startProcessAndWatch($process, $output);
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getServerUri(InputInterface $input)
    {
        return $this->getServerIp($input) . ':' . $this->getServerPort($input);
    }

    /**
     * Return the document root
     *
     * @param InputInterface $input
     * @return string
     */
    private function getDocumentRoot(InputInterface $input): string
    {
        if ($input->hasArgument('document-root') && $input->getArgument('document-root')) {
            $documentRoot = $input->getArgument('document-root');
            if ($documentRoot === filter_var($documentRoot, FILTER_SANITIZE_STRING)) {
                return $documentRoot;
            }

            throw new InvalidArgumentsException('Invalid input for argument "document-root"', 1420812213);
        }

        return ConfigurationManager::getSharedInstance()->getBasePath();
    }
}
