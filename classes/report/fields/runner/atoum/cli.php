<?php

namespace atoum\atoum\report\fields\runner\atoum;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\atoum
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
        return ($this->author === null || $this->version === null ? '' : $this->prompt . $this->colorizer->colorize($this->locale->_('atoum version %s by %s (%s)', $this->version, $this->author, $this->path)) . PHP_EOL);
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
