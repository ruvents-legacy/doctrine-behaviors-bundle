<?php
declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Normalizer;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class DoctrineIdentifierNormalizer implements ContextAwareNormalizerInterface, ContextAwareDenormalizerInterface
{
    const DOCTRINE_IDENTIFIER = 'doctrine_identifier';

    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $options = [])
    {
        return isset($options[self::DOCTRINE_IDENTIFIER])
            && true === $options[self::DOCTRINE_IDENTIFIER]
            && is_object($data)
            && null !== ($manager = $this->doctrine->getManagerForClass(get_class($data)))
            && $manager->contains($data);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        return $this->doctrine
            ->getManagerForClass($class = get_class($object))
            ->getClassMetadata($class)
            ->getIdentifierValues($object);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $options = [])
    {
        return class_exists($type)
            && is_array($data)
            && null !== $this->doctrine->getManagerForClass($type);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return $this->doctrine
            ->getManagerForClass($class)
            ->find($class, $data);
    }
}
