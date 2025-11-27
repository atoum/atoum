<?php

namespace atoum\atoum\cli;

class prompt
{
    protected string $value = '';
    protected ?colorizer $colorizer = null;

    public function __construct(string $value = '', ?colorizer $colorizer = null)
    {
        if ($colorizer === null) {
            $colorizer = new colorizer();
        }

        $this
            ->setValue($value)
            ->setColorizer($colorizer)
        ;
    }

    public function __toString(): string
    {
        return $this->colorizer->colorize($this->value);
    }

    public function setValue(string $value): static
    {
        $this->value = (string) $value;

        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setColorizer(colorizer $colorizer): static
    {
        $this->colorizer = $colorizer;

        return $this;
    }

    public function getColorizer(): colorizer
    {
        return $this->colorizer;
    }
}
