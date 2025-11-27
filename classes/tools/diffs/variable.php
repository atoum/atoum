<?php

namespace atoum\atoum\tools\diffs;

use atoum\atoum\exceptions;
use atoum\atoum\tools;

class variable extends tools\diff
{
    protected ?tools\variable\analyzer $analyzer = null;

    public function __construct(mixed $expected = null, mixed $actual = null)
    {
        $this->setAnalyzer();

        parent::__construct($expected, $actual);
    }

    public function setAnalyzer(?tools\variable\analyzer $analyzer = null): static
    {
        $this->analyzer = $analyzer ?: new tools\variable\analyzer();

        return $this;
    }

    public function getAnalyzer(): tools\variable\analyzer
    {
        return $this->analyzer;
    }

    public function setExpected(mixed $mixed): static
    {
        return parent::setExpected($this->analyzer->dump($mixed));
    }

    public function setActual(mixed $mixed): static
    {
        return parent::setActual($this->analyzer->dump($mixed));
    }

    public function make(mixed $expected = null, mixed $actual = null): array
    {
        if ($expected !== null) {
            $this->setExpected($expected);
        }

        if ($expected !== null) {
            $this->setActual($actual);
        }

        if ($this->expected === null) {
            throw new exceptions\runtime('Expected is undefined');
        }

        if ($this->actual === null) {
            throw new exceptions\runtime('Actual is undefined');
        }

        return parent::make();
    }
}
