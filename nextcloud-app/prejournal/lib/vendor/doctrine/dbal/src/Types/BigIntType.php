<?php

namespace Doctrine\DBAM\Types;

use Doctrine\DBAM\ParameterType;
use Doctrine\DBAM\Platforms\AbstractPlatform;

/**
 * Type that maps a database BIGINT to a PHP string.
 */
class BigIntType extends Type implements PhpIntegerMappingType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return Types::BIGINT;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getBigIntTypeDeclarationSQL($column);
    }

    /**
     * {@inheritdoc}
     */
    public function getBindingType()
    {
        return ParameterType::STRING;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value === null ? null : (string) $value;
    }
}
