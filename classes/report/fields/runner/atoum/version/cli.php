<?php

namespace atoum\atoum\report\fields\runner\atoum\version;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\atoum\version
{
    protected ?prompt $titlePrompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?prompt $versionPrompt = null;
    protected ?colorizer $versionColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setTitlePrompt()
            ->setTitleColorizer()
            ->setVersionColorizer()
        ;
    }

    public function __toString(): string
    {
        return
            $this->titlePrompt .
            sprintf(
                '%s: %s',
                $this->titleColorizer->colorize($this->locale->_('atoum version')),
                $this->versionColorizer->colorize(rtrim($this->version))
            ) .
            PHP_EOL
        ;
    }

    public function setTitlePrompt(?prompt $prompt = null): static
    {
        $this->titlePrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getTitlePrompt(): prompt
    {
        return $this->titlePrompt;
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

    public function setVersionColorizer(?colorizer $colorizer = null): static
    {
        $this->versionColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getVersionColorizer(): colorizer
    {
        return $this->versionColorizer;
    }
}
