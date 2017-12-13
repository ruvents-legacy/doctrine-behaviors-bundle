<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Ruvents\DoctrineBundle\Metadata\MetadataFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SearchIndexUpdateCommand extends Command
{
    private $doctrine;
    private $metadataFactory;

    public function __construct(ManagerRegistry $doctrine, MetadataFactoryInterface $metadataFactory)
    {
        $this->doctrine = $doctrine;
        $this->metadataFactory = $metadataFactory;

        parent::__construct('ruvents-doctrine:search-index:update');

        $this->addArgument('class', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $class = $input->getArgument('class');
        $manager = $this->doctrine->getManagerForClass($class);

        if (!$manager instanceof EntityManagerInterface) {
            throw new \UnexpectedValueException(sprintf('Class "%s" is not an entity.', $class));
        }

        $entityMetadata = $manager->getClassMetadata($class);
        $metadata = $this->metadataFactory->getMetadata($class);
        $i = 1;

        foreach ($manager->getRepository($class)->findAll() as $entity) {
            foreach ($metadata->getSearchIndexes() as $property => $searchIndex) {
                $entityMetadata->setFieldValue($entity, $property, ' __not_an_index__ ');
            }

            if (0 === $i % 100) {
                $manager->flush();
            }
        }

        $manager->flush();

        $output->writeln('<info>Done</info>');
    }
}
