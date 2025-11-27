<?php

namespace atoum\atoum\report\fields\runner\coverage;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\coverage
{
    protected ?prompt $prompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?colorizer $coverageColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setPrompt()
            ->setTitleColorizer()
            ->setCoverageColorizer()
        ;
    }

    public function __toString(): string
    {
        return $this->prompt .
            sprintf(
                '%s: %s.',
                $this->titleColorizer->colorize($this->locale->_('Code coverage')),
                $this->coverageColorizer->colorize(
                    $this->coverage === null
                    ?
                    $this->locale->_('unknown')
                    :
                    $this->locale->_('%3.2f%%', round($this->coverage->getValue() * 100, 2))
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

    public function setCoverageColorizer(?colorizer $colorizer = null): static
    {
        $this->coverageColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getCoverageColorizer(): colorizer
    {
        return $this->coverageColorizer;
    }
}
