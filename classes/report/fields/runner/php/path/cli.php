<?php

namespace atoum\atoum\report\fields\runner\php\path;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\php\path
{
    protected ?prompt $prompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?colorizer $pathColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setPrompt()
            ->setTitleColorizer()
            ->setPathColorizer()
        ;
    }

    public function __toString(): string
    {
        return
            $this->prompt .
            sprintf(
                $this->locale->_('%1$s: %2$s'),
                $this->titleColorizer->colorize($this->locale->_('PHP path')),
                $this->pathColorizer->colorize($this->path)
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

    public function setPathColorizer(?colorizer $colorizer = null): static
    {
        $this->pathColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getPathColorizer(): colorizer
    {
        return $this->pathColorizer;
    }
}
