<?php

namespace atoum\atoum\mock\php\method;

class argument
{
    protected ?string $type = null;
    protected bool $isReference = false;
    protected string $name = '';
    protected mixed $defaultValue = null;
    protected bool $defaultValueIsSet = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function __toString(): string
    {
        $string = '$' . $this->name;

        if ($this->isReference === true) {
            $string = '& ' . $string;
        }

        if ($this->type !== null) {
            $type = $this->type;

            if ($this->defaultValueIsSet === true && $this->defaultValue === null) {
                $type = '?' . $type;
            }

            $string = $type . ' ' . $string;
        }

        if ($this->defaultValueIsSet === true) {
            $string .= '=' . var_export($this->defaultValue, true);
        }

        return $string;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVariable(): string
    {
        return '$' . $this->name;
    }

    public function isObject(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isArray(): static
    {
        $this->type = 'array';

        return $this;
    }

    public function isUntyped(): static
    {
        $this->type = null;

        return $this;
    }

    public function isReference(): static
    {
        $this->isReference = true;

        return $this;
    }

    public function setDefaultValue(mixed $defaultValue): static
    {
        $this->defaultValue = $defaultValue;
        $this->defaultValueIsSet = true;

        return $this;
    }

    public static function get(string $name): static
    {
        return new static($name);
    }
}
