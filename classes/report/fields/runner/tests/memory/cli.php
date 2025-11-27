<?php

namespace atoum\atoum\report\fields\runner\tests\memory;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\tests\memory
{
    protected ?prompt $prompt = null;
    protected ?colorizer $memoryColorizer = null;
    protected ?colorizer $titleColorizer = null;

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
        $title = $this->locale->__('Total test memory usage', 'Total tests memory usage', $this->testNumber);

        if ($this->value === null) {
            $memory = $this->locale->_('unknown');
        } else {
            $memory = $this->locale->_('%4.2f Mb', $this->value / 1048576);
        }

        return $this->prompt . $this->locale->_('%s: %s.', $this->titleColorizer->colorize($title), $this->memoryColorizer->colorize($memory)) . PHP_EOL;
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
