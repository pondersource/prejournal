<?php

namespace Doctrine\DBAM\Types;

use Doctrine\DBAM\ParameterType;
use Doctrine\DBAM\Platforms\AbstractPlatform;
use Doctrine\DBAM\Platforms\DB2Platform;

/**
 * Type that maps an SQL boolean to a PHP boolean.
 */
class BooleanType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getBooleanTypeDeclarationSQL($column);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $platform->convertBooleansToDatabaseValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $platform->convertFromBoolean($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Types::BOOLEAN;
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return ParameterType::BOOLEAN;
    }

    /**
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        // We require a commented boolean type in order to distinguish between
        // boolean and smallint as both (have to) map to the same native type.
        return $platform instanceof DB2Platform;
    }
}
