<?php

namespace App\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UtcDateTimeType extends DateTimeType
{
    /**
     * @var \DateTimeZone
     */
    private static $utc;

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof \DateTime) {
            $value->setTimezone($this->getUtcTimezone());
        }

        return parent::convertToDatabaseValue($value, $platform);
    }

    private function getUtcTimezone(): \DateTimeZone
    {
        if (null === self::$utc) {
            self::$utc = new \DateTimeZone('UTC');
        }

        return self::$utc;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || $value instanceof \DateTimeInterface) {
            return $value;
        }

        $dateTime = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, $this->getUtcTimezone());

        if (!$dateTime) {
            $dateTime = date_create($value);
        }

        if (!$dateTime) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
        }

        return $dateTime;
    }
}
