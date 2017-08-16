<?php

namespace Ruvents\DoctrineBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;

class JsonbType extends JsonArrayType
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'JSONB';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jsonb';
    }
}
