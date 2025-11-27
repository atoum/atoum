<?php

namespace atoum\atoum\php\tokenizer\iterators;

use atoum\atoum\php\tokenizer;
use atoum\atoum\php\tokenizer\iterators;

class phpClass extends tokenizer\iterator
{
    protected array $methods = [];
    protected array $constants = [];
    protected array $properties = [];

    public function reset(): static
    {
        $this->methods = [];
        $this->constants = [];
        $this->properties = [];

        return parent::reset();
    }

    public function getConstant(int $index): ?iterators\phpConstant
    {
        return (isset($this->constants[$index]) === false ? null : $this->constants[$index]);
    }

    public function getConstants(): array
    {
        return $this->constants;
    }

    public function appendConstant(iterators\phpConstant $phpConstant): static
    {
        $this->constants[] = $phpConstant;

        return $this->append($phpConstant);
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getMethod(int $index): ?iterators\phpMethod
    {
        return (isset($this->methods[$index]) === false ? null : $this->methods[$index]);
    }

    public function appendMethod(iterators\phpMethod $phpMethod): static
    {
        $this->methods[] = $phpMethod;

        return $this->append($phpMethod);
    }

    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getProperty(int $index): ?iterators\phpProperty
    {
        return (isset($this->properties[$index]) === false ? null : $this->properties[$index]);
    }

    public function appendProperty(iterators\phpProperty $phpProperty): static
    {
        $this->properties[] = $phpProperty;

        return $this->append($phpProperty);
    }
}
