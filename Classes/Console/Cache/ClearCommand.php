<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Cache;

use Cundd\Stairtower\Configuration\ConfigurationManager;
use Cundd\Stairtower\RuntimeException;
use Cundd\Stairtower\Utility\GeneralUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * Console command to clear the caches
 */
class ClearCommand extends Command
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clear the cache');
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
        $cacheDirectory = ConfigurationManager::getSharedInstance()->getConfigurationForKeyPath('cachePath');
        if (GeneralUtility::removeDirectoryRecursive($cacheDirectory)) {
            $output->writeln(sprintf('<info>Did clear the cache</info>'));
        } else {
            throw new RuntimeException(sprintf('Could not clear caches in directory %s', $cacheDirectory), 1415824139);
        }
    }
}