<?php

namespace atoum\atoum\report\fields\test\memory;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\test\memory
{
    protected ?prompt $prompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?colorizer $memoryColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setPrompt()
            ->setTitleColorizer()
            ->setMemoryColorizer()
        ;
    }

    public function __toString(): string
    {
        return $this->prompt .
            sprintf(
                $this->locale->_('%1$s: %2$s.'),
                $this->titleColorizer->colorize($this->locale->_('Memory usage')),
                $this->memoryColorizer->colorize(
                    $this->value === null
                    ?
                    $this->locale->_('unknown')
                    :
                    sprintf(
                        $this->locale->_('%4.2f Mb'),
                        $this->value / 1048576
                    )
                )
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

    public function setTitleColorizer(?colorizer $colorizer = null): static
    {
        $this->titleColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getTitleColorizer(): colorizer
    {
        return $this->titleColorizer;
    }

    public function setMemoryColorizer(?colorizer $colorizer = null): static
    {
        $this->memoryColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getMemoryColorizer(): colorizer
    {
        return $this->memoryColorizer;
    }
}
