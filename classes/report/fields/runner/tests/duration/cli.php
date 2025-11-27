<?php

namespace atoum\atoum\report\fields\runner\tests\duration;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\tests\duration
{
    protected ?prompt $prompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?colorizer $durationColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setPrompt()
            ->setTitleColorizer()
            ->setDurationColorizer()
        ;
    }

    public function __toString(): string
    {
        return $this->prompt .
            sprintf(
                $this->locale->_('%s: %s.'),
                $this->titleColorizer->colorize($this->locale->__('Total test duration', 'Total tests duration', $this->testNumber)),
                $this->durationColorizer->colorize(
                    sprintf(
                        $this->value === null ? $this->locale->_('unknown') : $this->locale->__('%4.2f second', '%4.2f seconds', $this->value),
                        $this->value
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

    public function setTitleColorizer(?colorizer $titleColorizer = null): static
    {
        $this->titleColorizer = $titleColorizer ?: new colorizer();

        return $this;
    }

    public function getTitleColorizer(): colorizer
    {
        return $this->titleColorizer;
    }

    public function setDurationColorizer(?colorizer $durationColorizer = null): static
    {
        $this->durationColorizer = $durationColorizer ?: new colorizer();

        return $this;
    }

    public function getDurationColorizer(): colorizer
    {
        return $this->durationColorizer;
    }
}
