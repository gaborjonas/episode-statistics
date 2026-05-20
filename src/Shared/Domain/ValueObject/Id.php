<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract class Id
{
    protected string $value;

    abstract public function __construct(string $value);

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    protected static function isValid(string $value): bool
    {
        return (bool)preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $value,
        );
    }
}
