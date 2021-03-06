<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Data;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list data
 */
class RemoveCommand extends AbstractDataCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('data:remove')
            ->setDescription('Remove the data with the given identifier from the given database')
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
        $document = $this->findDataInstanceFromInput($input);
        if ($document === null) {
            return;
        }
        $database = $this->findDatabaseInstanceFromInput($input);
        $database->remove($document);
        $objectIdentifier = $document->getId();

        $this->coordinator->commitDatabase($database);

        if (!$database->contains($document)) {
            $output->writeln(
                sprintf(
                    '<info>Object with ID %s was deleted from database %s</info>',
                    $objectIdentifier,
                    $database->getIdentifier()
                )
            );
        } else {
            $output->writeln(
                sprintf(
                    '<info>Object with ID %s could not be deleted from database %s</info>',
                    $objectIdentifier,
                    $database->getIdentifier()
                )
            );
        }
    }
}
