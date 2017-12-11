<?php
declare(strict_types=1);

use Ruvents\DoctrineBundle\Translations\TranslationsInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TranslationsNormalizer implements NormalizerInterface, DenormalizerInterface
{
    const OBJECT_TO_POPULATE = AbstractNormalizer::OBJECT_TO_POPULATE;

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof TranslationsInterface;
    }

    /**
     * {@inheritdoc}
     *
     * @param TranslationsInterface $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $getter = \Closure::bind(function (string $locale) use ($object) {
            return $object->$locale;
        }, null, get_class($object));

        $data = [];

        foreach ($object::getLocalesMap() as $locale => $nb) {
            $data[$locale] = $getter($locale);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return class_exists($type) && is_subclass_of($type, TranslationsInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
    }
}
