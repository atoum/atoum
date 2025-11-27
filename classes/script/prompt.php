<?php

namespace atoum\atoum\script;

use atoum\atoum\reader;
use atoum\atoum\readers\std;
use atoum\atoum\writer;
use atoum\atoum\writers;

class prompt
{
    protected ?reader $inputReader = null;
    protected ?writer $outputWriter = null;

    public function __construct()
    {
        $this
            ->setInputReader()
            ->setOutputWriter()
        ;
    }

    public function getInputReader(): reader
    {
        return $this->inputReader;
    }

    public function setInputReader(?reader $inputReader = null): static
    {
        $this->inputReader = $inputReader ?: new std\in();

        return $this;
    }

    public function getOutputWriter(): writer
    {
        return $this->outputWriter;
    }

    public function setOutputWriter(?writer $writer = null): static
    {
        $this->outputWriter = $writer ?: new writers\std\out();

        return $this;
    }

    public function ask(string $message): string
    {
        $this->outputWriter->write($message);

        return $this->inputReader->read();
    }
}
