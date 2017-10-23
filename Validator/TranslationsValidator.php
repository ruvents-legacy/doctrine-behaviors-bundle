<?php

namespace Ruvents\DoctrineBundle\Validator;

use Ruvents\DoctrineBundle\Translations\TranslationsInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class TranslationsValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Translations) {
            throw new UnexpectedTypeException($constraint, Translations::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof TranslationsInterface) {
            throw new UnexpectedTypeException($value, TranslationsInterface::class);
        }

        $reflectionClass = new \ReflectionClass($value);

        foreach ($constraint->locales as $locale => $constraints) {
            if (!isset($value->getLocalesMap()[$locale])) {
                throw new \OutOfRangeException(
                    sprintf('Locale "%s" is not defined in %s.', $locale, get_class($value))
                );
            }

            $this->context
                ->getValidator()
                ->inContext($this->context)
                ->atPath($locale)
                ->validate($this->getValue($reflectionClass, $value, $locale), $constraints);
        }
    }

    private function getValue(\ReflectionClass $reflectionClass, $object, $property)
    {
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }
}
