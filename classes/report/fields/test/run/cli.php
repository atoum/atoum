<?php

namespace atoum\atoum\report\fields\test\run;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\test\run
{
    protected ?prompt $prompt = null;
    protected ?colorizer $colorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setPrompt()
            ->setColorizer()
        ;
    }

    public function __toString(): string
    {
        return $this->prompt .
            (
                $this->testClass === null
                ?
                $this->colorizer->colorize($this->locale->_('There is currently no test running.'))
                :
                $this->locale->_('%s...', $this->colorizer->colorize($this->testClass))
            ) .
            PHP_EOL
        ;
    }

    public function setPrompt(?prompt $prompt = null): static
    {
        $this->prompt = $prompt ?: new prompt();

        return $this;
    }

    public function getPrompt(): prompt
    {
        return $this->prompt;
    }

    public function setColorizer(?colorizer $colorizer = null): static
    {
        $this->colorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getColorizer(): colorizer
    {
        return $this->colorizer;
    }
}
