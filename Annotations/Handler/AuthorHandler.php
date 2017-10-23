<?php

namespace Ruvents\DoctrineBundle\Annotations\Handler;

use Doctrine\Common\Annotations\Annotation\Target;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\Annotations\Handler\AuthorStrategy\AuthorStrategyInterface;
use Ruvents\DoctrineBundle\Annotations\Map;
use Ruvents\DoctrineBundle\Annotations\Mapping\Author;

class AuthorHandler implements HandlerInterface
{
    /**
     * @var AuthorStrategyInterface
     */
    private $strategy;

    public function __construct(AuthorStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * {@inheritdoc}
     */
    public static function supportsAnnotation($annotation, int $target): bool
    {
        return Target::TARGET_PROPERTY === $target && $annotation instanceof Author;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args, Map $map)
    {
        $entity = $args->getEntity();
        $metadata = $args->getEntityManager()->getClassMetadata(get_class($entity));

        foreach ($map->getPropertyAnnotations() as $property => $annotations) {
            if (isset($metadata->fieldNames[$property])) {
                $value = $this->strategy->getFieldAuthor(
                    $annotations[0],
                    $metadata->getTypeOfField($property),
                    $metadata->getFieldValue($entity, $property)
                );
            } else {
                $value = $this->strategy->getAssociationAuthor(
                    $annotations[0],
                    $metadata->getAssociationTargetClass($property),
                    $metadata->getFieldValue($entity, $property)
                );
            }

            $metadata->setFieldValue($entity, $property, $value);
        }
    }
}
