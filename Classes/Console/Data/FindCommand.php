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
 * Console command to find data
 *
 * @package Cundd\PersistentObjectStore\Console
 */
class FindCommand extends AbstractDataCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('data:find')
            ->setDescription('Look up the data for the given identifier in a databases')
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
        if ($document) {
            $output->write($this->serializer->serialize($document->getData()));
        } else {
            $output->write(sprintf('<info>Object with ID %s not found in database %s</info>',
                $input->getArgument('identifier'), $input->getArgument('database')));
        }
    }
} 