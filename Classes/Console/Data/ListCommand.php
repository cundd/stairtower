<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Data;

use Cundd\Stairtower\Console\AbstractCommand;
use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list data
 */
class ListCommand extends AbstractCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('data:list')
            ->setDescription('List data from a databases')
            ->addArgument(
                'identifier',
                InputArgument::REQUIRED,
                'Unique name of the database to create'
            )
            ->setAliases(['database:show']);
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
        $databaseIdentifier = $input->getArgument('identifier');
        if (!$databaseIdentifier) {
            throw new \InvalidArgumentException('Missing database identifier argument', 1412524227);
        }
        $database = $this->coordinator->getDatabase($databaseIdentifier);

        /** @var DocumentInterface $document */
        foreach ($database as $document) {
            $description = sprintf('%s', $document->getGuid());
            $output->writeln($description);
        }
    }
} 