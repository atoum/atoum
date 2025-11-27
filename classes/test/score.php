<?php

namespace atoum\atoum\test;

use atoum\atoum;

class score extends atoum\score
{
    private ?string $case = null;
    private mixed $dataSetKey = null;
    private ?string $dataSetProvider = null;

    public function reset(): static
    {
        return parent::reset()
            ->unsetCase()
            ->unsetDataSet()
        ;
    }

    public function addFail(string $file, string $class, ?string $method, ?int $line, string $asserter, string $reason, ?string $case = null, mixed $dataSetKey = null, ?string $dataSetProvider = null): int
    {
        return parent::addFail($file, $class, $method, $line, $asserter, $reason, $case ?: $this->case, $dataSetKey ?: $this->dataSetKey, $dataSetProvider ?: $this->dataSetProvider);
    }

    public function addException(string $file, string $class, string $method, int|string $line, \exception $exception, ?string $case = null, mixed $dataSetKey = null, ?string $dataSetProvider = null): static
    {
        return parent::addException($file, $class, $method, $line, $exception, $case ?: $this->case, $dataSetKey ?: $this->dataSetKey, $dataSetProvider ?: $this->dataSetProvider);
    }

    public function addError(string $file, string $class, ?string $method, int $line, int|string $type, string $message, ?string $errorFile = null, ?int $errorLine = null, ?string $case = null, mixed $dataSetKey = null, ?string $dataSetProvider = null): static
    {
        return parent::addError($file, $class, $method, $line, $type, $message, $errorFile, $errorLine, $case ?: $this->case, $dataSetKey ?: $this->dataSetKey, $dataSetProvider ?: $this->dataSetProvider);
    }

    public function getCase(): ?string
    {
        return $this->case;
    }

    public function setCase(string $case): static
    {
        $this->case = (string) $case;

        return $this;
    }

    public function unsetCase(): static
    {
        $this->case = null;

        return $this;
    }

    public function setDataSet(mixed $key, string $dataProvider): static
    {
        $this->dataSetKey = $key;
        $this->dataSetProvider = $dataProvider;

        return $this;
    }

    public function unsetDataSet(): static
    {
        $this->dataSetKey = null;
        $this->dataSetProvider = null;

        return $this;
    }

    public function getDataSetKey(): mixed
    {
        return $this->dataSetKey;
    }

    public function getDataSetProvider(): ?string
    {
        return $this->dataSetProvider;
    }
}
