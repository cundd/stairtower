<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Data;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to show data
 */
class ShowCommand extends AbstractDataCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('data:show')
            ->setDescription('Show an entry from a database')
            ->addArgument(
                self::ARGUMENT_DATABASE_ID,
                InputArgument::REQUIRED,
                'Unique name of the database to search in'
            )
            ->addArgument(
                self::ARGUMENT_DOCUMENT_ID,
                InputArgument::REQUIRED,
                'Document identifier to search for'
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
        $document = $this->findDataInstanceFromInput($input, true);
        if ($document) {
            $output->write($this->formatter->format($document->getData()));
        } else {
            $output->writeln(
                sprintf(
                    '<error>Document with identifier "%s" not found in Database "%s"</error>',
                    $input->getArgument(self::ARGUMENT_DOCUMENT_ID),
                    $input->getArgument(self::ARGUMENT_DATABASE_ID)
                )
            );
        }
    }
}
