<?php

namespace Ruvents\DoctrineBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CompositeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Composite) {
            throw new UnexpectedTypeException($constraint, Composite::class);
        }

        $this->context
            ->getValidator()
            ->inContext($this->context)
            ->validate($value, $constraint->constraints);
    }
}
