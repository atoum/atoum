<?php

namespace atoum\atoum\php;

class extension
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function isLoaded(): bool
    {
        return extension_loaded($this->name);
    }

    public function requireExtension(): static
    {
        if ($this->isLoaded() === false) {
            throw new exception('PHP extension \'' . $this->name . '\' is not loaded');
        }

        return $this;
    }
}
