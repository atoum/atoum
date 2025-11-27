<?php

namespace atoum\atoum\php\tokenizer\iterators;

use atoum\atoum\php\tokenizer;
use atoum\atoum\php\tokenizer\iterators;

class phpScript extends tokenizer\iterators\phpNamespace
{
    protected array $namespaces = [];
    protected array $importations = [];

    public function reset(): static
    {
        $this->namespaces = [];

        return parent::reset();
    }

    public function appendNamespace(iterators\phpNamespace $phpNamespace): static
    {
        $this->namespaces[] = $phpNamespace;

        return $this->append($phpNamespace);
    }

    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    public function getNamespace(int $index): ?iterators\phpNamespace
    {
        return (isset($this->namespaces[$index]) === false ? null : $this->namespaces[$index]);
    }

    public function appendImportation(iterators\phpImportation $phpImportation): static
    {
        $this->importations[] = $phpImportation;

        return $this->append($phpImportation);
    }

    public function getImportations(): array
    {
        return $this->importations;
    }

    public function getImportation(int $index): ?iterators\phpImportation
    {
        return (isset($this->importations[$index]) === false ? null : $this->importations[$index]);
    }
}
