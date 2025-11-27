<?php

namespace atoum\atoum;

class locale
{
    protected ?string $value = null;

    public function __construct(?string $value = null)
    {
        if ($value !== null) {
            $this->set($value);
        }
    }

    public function __toString(): string
    {
        return ($this->value === null ? 'unknown' : $this->value);
    }

    public function set(string $value): static
    {
        $this->value = (string) $value;

        return $this;
    }

    public function get(): ?string
    {
        return $this->value;
    }

    public function _(string $string, mixed ...$arguments): string
    {
        return self::format($string, $arguments);
    }

    public function __(string $singular, string $plural, int|float|null $quantity, mixed ...$arguments): string
    {
        return self::format(($quantity ?? 0) <= 1 ? $singular : $plural, $arguments);
    }

    private static function format(string $string, array $arguments): string
    {
        if (count($arguments) > 0) {
            $string = vsprintf($string, $arguments);
        }

        return $string;
    }
}
