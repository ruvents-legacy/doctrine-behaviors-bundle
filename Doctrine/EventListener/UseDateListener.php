<?php

namespace Ruvents\DoctrineBundle\Doctrine\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\DoctrineBundle\Mapping\Factory\ClassMetadataFactoryInterface;
use Ruvents\DoctrineBundle\Mapping\Metadata\UseDateMetadataInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class UseDateListener implements EventSubscriber
{
    /**
     * @var ClassMetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * @param ClassMetadataFactoryInterface  $metadataFactory
     * @param PropertyAccessorInterface|null $accessor
     */
    public function __construct(
        ClassMetadataFactoryInterface $metadataFactory,
        PropertyAccessorInterface $accessor = null
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->accessor = $accessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if (!$this->metadataFactory->hasMetadataFor($entity)) {
            return;
        }

        $entityMetadata = $event->getEntityManager()->getClassMetadata(get_class($entity));
        $metadata = $this->metadataFactory->getMetadataFor($entity);

        if (!$metadata instanceof UseDateMetadataInterface) {
            return;
        }

        foreach ($metadata->getUseDateMappings() as $property => $mapping) {
            /**
             * @var \DateTime $date
             * @var \DateTime $value
             */
            $date = $this->accessor->getValue($entity, $mapping->propertyPath);
            $value = $entityMetadata->getFieldValue($entity, $property);

            $value->setDate($date->format('Y'), $date->format('m'), $date->format('d'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
        ];
    }
}
