<?php

namespace atoum\atoum\report\fields\runner\failures;

use atoum\atoum\adapter;
use atoum\atoum\report\fields\runner;

class execute extends runner\failures
{
    protected string $command = '';
    protected adapter $adapter;

    public function __construct(string $command)
    {
        parent::__construct();

        $this
            ->setCommand($command)
            ->setAdapter()
        ;
    }

    public function __toString(): string
    {
        if ($this->runner !== null) {
            $fails = [];

            foreach ($this->runner->getScore()->getFailAssertions() as $fail) {
                switch (true) {
                    case isset($fails[$fail['file']]) === false:
                    case $fails[$fail['file']] > $fail['line']:
                        $fails[$fail['file']] = $fail['line'];
                }
            }

            ksort($fails);

            foreach ($fails as $file => $line) {
                $this->adapter->system(sprintf($this->getCommand(), $file, $line));
            }
        }

        return '';
    }

    public function setCommand(string $command): static
    {
        $this->command = (string) $command;

        return $this;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setAdapter(?adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new adapter();

        return $this;
    }

    public function getAdapter(): adapter
    {
        return $this->adapter;
    }
}
