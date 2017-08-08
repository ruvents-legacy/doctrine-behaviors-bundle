<?php

namespace Ruvents\DoctrineBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;

class DateTimeImmutableType extends DateTimeType
{
    use ConvertToDateTimeImmutableTrait;

    /**
     * {@inheritdoc}
     */
    public function getFormatString(AbstractPlatform $platform)
    {
        return $platform->getDateTimeFormatString();
    }
}
