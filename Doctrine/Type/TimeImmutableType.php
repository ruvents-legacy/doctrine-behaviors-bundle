<?php

namespace Ruvents\DoctrineBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TimeType;

class TimeImmutableType extends TimeType
{
    use ConvertToDateTimeImmutableTrait;

    /**
     * {@inheritdoc}
     */
    public function getFormatString(AbstractPlatform $platform)
    {
        return '!'.$platform->getTimeFormatString();
    }
}
