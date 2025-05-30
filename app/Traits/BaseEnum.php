<?php

namespace App\Traits;

trait BaseEnum
{
    /**
     * Get the names of all cases as array
     */
    public static function namesToArray(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all the values of all cases as array
     */
    public static function valuesToArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get the associative array of the Enums cases and values
     */
    public static function toArray(): array
    {
        return array_combine(self::namesToArray(), self::valuesToArray());
    }

    /**
     * Returns enum values as a string.
     */
    public static function valuesToString(string $separator = ','): string
    {
        return implode($separator, self::valuesToArray());
    }

    /**
     * Returns enum names as a string.
     */
    public static function namesToString(string $separator = ','): string
    {
        return implode($separator, self::namesToArray());
    }

    /**
     * Cast string value to Enum class
     *
     * @param mixed $nameOrValue
     * @param bool $should_throw = true
     * @param null|\Exception $exception = null
     *
     * @return self|null
     *
     * @throws \BadMethodCallException
     */
    public static function cast(mixed $nameOrValue, bool $should_throw = true, ?\Exception $exception = null): self|null
    {
        foreach (self::cases() as $case) {
            if ($nameOrValue === $case->value || $nameOrValue === $case->name) {
                return $case;
            }
        }

        if (true === $should_throw) {
            throw $exception ?? new \BadMethodCallException("Invalid enum value or name: {$nameOrValue}");
        }

        return null;
    }
}
