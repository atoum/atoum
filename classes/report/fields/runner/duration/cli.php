<?php

namespace atoum\atoum\report\fields\runner\duration;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report\fields\runner\duration;

class cli extends duration
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
                $this->locale->_('%1$s: %2$s.'),
                $this->titleColorizer->colorize($this->locale->_('Running duration')),
                $this->durationColorizer->colorize($this->value === null ? $this->locale->_('unknown') : sprintf($this->locale->__('%4.2f second', '%4.2f seconds', $this->value), $this->value))
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

    public function setDurationColorizer(?colorizer $colorizer = null): static
    {
        $this->durationColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getDurationColorizer(): colorizer
    {
        return $this->durationColorizer;
    }
}
