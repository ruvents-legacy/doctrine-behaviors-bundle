<?php

namespace Ruvents\DoctrineBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateType;

class DateImmutableType extends DateType
{
    use ConvertToDateTimeImmutableTrait;

    /**
     * {@inheritdoc}
     */
    public function getFormatString(AbstractPlatform $platform)
    {
        return '!'.$platform->getDateFormatString();
    }
}
