<?php

declare(strict_types=1);

namespace Ruwork\DoctrineBehaviorsBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ruwork\DoctrineBehaviorsBundle\Metadata\MetadataFactoryInterface;
use Ruwork\DoctrineBehaviorsBundle\Strategy\AuthorStrategy\AuthorStrategyInterface;

class AuthorListener
{
    private $factory;
    private $strategy;

    public function __construct(MetadataFactoryInterface $factory, AuthorStrategyInterface $strategy)
    {
        $this->factory = $factory;
        $this->strategy = $strategy;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $class = get_class($entity);
        $metadata = $args->getEntityManager()->getClassMetadata($class);
        $authors = $this->factory->getMetadata($class)->getAuthors();

        foreach ($authors as $property => $author) {
            if ($author->overwrite || !$metadata->getFieldValue($entity, $property)) {
                $value = $this->strategy->getAuthor($metadata, $property);
                $metadata->setFieldValue($entity, $property, $value);
            }
        }
    }
}
