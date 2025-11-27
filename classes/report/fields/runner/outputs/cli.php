<?php

namespace atoum\atoum\report\fields\runner\outputs;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report\fields\runner\outputs;

class cli extends outputs
{
    protected ?prompt $titlePrompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?prompt $methodPrompt = null;
    protected ?colorizer $methodColorizer = null;
    protected ?prompt $outputPrompt = null;
    protected ?colorizer $outputColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setTitlePrompt()
            ->setTitleColorizer()
            ->setMethodPrompt()
            ->setMethodColorizer()
            ->setOutputPrompt()
            ->setOutputColorizer()
        ;
    }

    public function __toString(): string
    {
        $string = '';

        if ($this->runner !== null) {
            $outputs = $this->runner->getScore()->getOutputs();

            $sizeOfOutputs = count($outputs);

            if ($sizeOfOutputs > 0) {
                $string .=
                    $this->titlePrompt .
                    sprintf(
                        $this->locale->_('%s:'),
                        $this->titleColorizer->colorize(sprintf($this->locale->__('There is %d output', 'There are %d outputs', $sizeOfOutputs), $sizeOfOutputs))
                    ) .
                    PHP_EOL
                ;

                foreach ($outputs as $output) {
                    $string .= $this->methodPrompt . sprintf('%s:', $this->methodColorizer->colorize($this->locale->_('In %s::%s()', $output['class'], $output['method']))) . PHP_EOL;

                    foreach (explode(PHP_EOL, rtrim($output['value'])) as $line) {
                        $string .= $this->outputPrompt . $this->outputColorizer->colorize($line) . PHP_EOL;
                    }
                }
            }
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

    public function setMethodPrompt(?prompt $prompt = null): static
    {
        $this->methodPrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getMethodPrompt(): prompt
    {
        return $this->methodPrompt;
    }

    public function setMethodColorizer(?colorizer $colorizer = null): static
    {
        $this->methodColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getMethodColorizer(): colorizer
    {
        return $this->methodColorizer;
    }

    public function setOutputPrompt(?prompt $prompt = null): static
    {
        $this->outputPrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getOutputPrompt(): prompt
    {
        return $this->outputPrompt;
    }

    public function setOutputColorizer(?colorizer $colorizer = null): static
    {
        $this->outputColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getOutputColorizer(): colorizer
    {
        return $this->outputColorizer;
    }
}
