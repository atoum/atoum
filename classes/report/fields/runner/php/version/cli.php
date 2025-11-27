<?php

namespace atoum\atoum\report\fields\runner\php\version;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\php\version
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
            ->setVersionPrompt()
            ->setVersionColorizer()
        ;
    }

    public function __toString(): string
    {
        $string =
            $this->titlePrompt .
            sprintf(
                '%s:',
                $this->titleColorizer->colorize($this->locale->_('PHP version'))
            ) .
            PHP_EOL
        ;

        foreach (explode(PHP_EOL, $this->version) as $line) {
            $string .= $this->versionPrompt . $this->versionColorizer->colorize(rtrim($line)) . PHP_EOL;
        }

        return $string;
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

    public function setVersionPrompt(?prompt $prompt = null): static
    {
        $this->versionPrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getVersionPrompt(): prompt
    {
        return $this->versionPrompt;
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
