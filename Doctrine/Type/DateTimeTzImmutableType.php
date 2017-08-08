<?php

namespace Ruvents\DoctrineBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeTzType;

class DateTimeTzImmutableType extends DateTimeTzType
{
    use ConvertToDateTimeImmutableTrait;

    /**
     * {@inheritdoc}
     */
    public function getFormatString(AbstractPlatform $platform)
    {
        return $platform->getDateTimeTzFormatString();
    }
}
