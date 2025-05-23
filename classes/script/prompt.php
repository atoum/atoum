<?php

namespace atoum\atoum\script;

use atoum\atoum\reader;
use atoum\atoum\readers\std;
use atoum\atoum\writer;
use atoum\atoum\writers;

class prompt
{
    protected $inputReader = null;
    protected $outputWriter = null;

    public function __construct()
    {
        $this
            ->setInputReader()
            ->setOutputWriter()
        ;
    }

    public function getInputReader()
    {
        return $this->inputReader;
    }

    public function setInputReader(?reader $inputReader = null)
    {
        $this->inputReader = $inputReader ?: new std\in();

        return $this;
    }

    public function getOutputWriter()
    {
        return $this->outputWriter;
    }

    public function setOutputWriter(?writer $writer = null)
    {
        $this->outputWriter = $writer ?: new writers\std\out();

        return $this;
    }

    public function ask($message)
    {
        $this->outputWriter->write($message);

        return $this->inputReader->read();
    }
}
