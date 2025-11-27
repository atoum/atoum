<?php

namespace atoum\atoum\php\tokenizer\iterators;

use atoum\atoum\php\tokenizer;
use atoum\atoum\php\tokenizer\iterators;

class phpNamespace extends tokenizer\iterator
{
    protected array $constants = [];
    protected array $functions = [];
    protected array $classes = [];

    public function reset(): static
    {
        $this->functions = [];
        $this->constants = [];
        $this->classes = [];

        return parent::reset();
    }

    public function getConstants(): array
    {
        return $this->constants;
    }

    public function getConstant(int $index): ?iterators\phpConstant
    {
        return (isset($this->constants[$index]) === false ? null : $this->constants[$index]);
    }

    public function appendConstant(iterators\phpConstant $phpConstant): static
    {
        $this->constants[] = $phpConstant;

        return $this->append($phpConstant);
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getClass(int $index): ?iterators\phpClass
    {
        return (isset($this->classes[$index]) === false ? null : $this->classes[$index]);
    }

    public function appendClass(iterators\phpClass $phpClass): static
    {
        $this->classes[] = $phpClass;

        return $this->append($phpClass);
    }

    public function getFunctions(): array
    {
        return $this->functions;
    }

    public function getFunction(int $index): ?iterators\phpFunction
    {
        return (isset($this->functions[$index]) === false ? null : $this->functions[$index]);
    }

    public function appendFunction(iterators\phpFunction $phpFunction): static
    {
        $this->functions[] = $phpFunction;

        return $this->append($phpFunction);
    }
}
