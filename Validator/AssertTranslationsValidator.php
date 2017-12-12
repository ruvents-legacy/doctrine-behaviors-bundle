<?php

declare(strict_types=1);

namespace Ruvents\DoctrineBundle\Validator;

use Ruvents\DoctrineBundle\Translations\TranslationsInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AssertTranslationsValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AssertTranslations) {
            throw new UnexpectedTypeException($constraint, AssertTranslations::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof TranslationsInterface) {
            throw new UnexpectedTypeException($value, TranslationsInterface::class);
        }

        foreach ($constraint->locales as $locale => $constraints) {
            $this->context
                ->getValidator()
                ->inContext($this->context)
                ->atPath($locale)
                ->validate($value->get($locale), $constraints);
        }
    }
}
