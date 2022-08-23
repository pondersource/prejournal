<?php

declare(strict_types=1);

namespace Doctrine\DBAM\Types;

use Doctrine\DBAM\ParameterType;
use Doctrine\DBAM\Platforms\AbstractPlatform;

final class AsciiStringType extends StringType
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getAsciiStringTypeDeclarationSQL($column);
    }

    public function getBindingType(): int
    {
        return ParameterType::ASCII;
    }

    public function getName(): string
    {
        return Types::ASCII_STRING;
    }
}
