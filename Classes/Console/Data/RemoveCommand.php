<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console\Data;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list data
 *
 * @package Cundd\PersistentObjectStore\Console
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
                'database',
                InputArgument::REQUIRED,
                'Unique name of the database to search in'
            )
            ->addArgument(
                'identifier',
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
            $output->writeln(sprintf('<info>Object with ID %s was deleted from database %s</info>', $objectIdentifier,
                $database->getIdentifier()));
        } else {
            $output->writeln(sprintf('<info>Object with ID %s could not be deleted from database %s</info>',
                $objectIdentifier, $database->getIdentifier()));
        }
    }
}