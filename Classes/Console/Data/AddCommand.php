<?php
declare(strict_types=1);

namespace Cundd\Stairtower\Console\Data;


use Cundd\Stairtower\Domain\Model\DocumentInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console command to list data
 */
class AddCommand extends AbstractDataCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('data:add')
            ->setDescription('Add an entry to the database')
            ->addArgument(
                self::ARGUMENT_DATABASE_ID,
                InputArgument::REQUIRED,
                'Unique name of the database to search in'
            )
            ->addArgument(
                'content',
                InputArgument::REQUIRED,
                'JSON encoded data to add to the database'
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
        $database = $this->findDatabaseInstanceFromInput($input);

        /** @var DocumentInterface $document */
        $document = $this->serializer->unserialize($input->getArgument('content'));
        if (!$document) {
            $output->writeln('<error>Could not create object</error>');
        }
        $database->add($document);
        $objectIdentifier = $document->getId();

        $this->coordinator->commitDatabase($database);

        if ($database->contains($document)) {
            $output->writeln(
                sprintf(
                    '<info>Object with ID %s was add to database %s</info>',
                    $objectIdentifier,
                    $database->getIdentifier()
                )
            );
        } else {
            $output->writeln(
                sprintf(
                    '<info>Object with ID %s could not be add to database %s</info>',
                    $objectIdentifier,
                    $database->getIdentifier()
                )
            );
        }
    }
}
