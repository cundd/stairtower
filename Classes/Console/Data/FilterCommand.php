<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 05.10.14
 * Time: 16:58
 */

namespace Cundd\PersistentObjectStore\Console\Data;


use Cundd\PersistentObjectStore\Domain\Model\DatabaseInterface;
use Cundd\PersistentObjectStore\Domain\Model\DocumentInterface;
use Cundd\PersistentObjectStore\Filter\FilterResult;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to find data
 *
 * @package Cundd\PersistentObjectStore\Console
 */
class FilterCommand extends AbstractDataCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('data:filter')
            ->setDescription('Filter the database by the given query')
            ->addArgument(
                'database',
                InputArgument::REQUIRED,
                'Unique name of the database to search in'
            )
            ->addArgument(
                'query',
                InputArgument::REQUIRED,
                'JSON formatted query'
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
        $result = $this->filterDataInstanceFromInput($input);
        if ($result instanceof DocumentInterface) {
            $output->write($this->formatter->format($result->getData()));
        } elseif ($result instanceof FilterResult) {
            $output->write($this->formatter->format($result->toArray()));
        } elseif ($result instanceof DatabaseInterface) {
            $output->write($this->formatter->format($result->toArray()));
            //$output->write($this->formatter->format($result-));
        } else {
            $output->write(sprintf('<info>Nothing found in database %s</info>',
                $input->getArgument('database')));
        }
    }
} 