<?php

namespace Ruvents\DoctrineBundle\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

trait ConvertToDateTimeImmutableTrait
{
    /**
     * @see Type::convertToPHPValue()
     *
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return \DateTimeImmutable|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($value);
        }

        $format = $this->getFormatString($platform);
        $value = \DateTimeImmutable::createFromFormat($format, $value);

        if (false === $value) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $format);
        }

        return $value;
    }

    /**
     * @see Type::getName()
     *
     * @return string
     */
    abstract public function getName();

    /**
     * @param AbstractPlatform $platform
     *
     * @return string
     */
    abstract public function getFormatString(AbstractPlatform $platform);
}
