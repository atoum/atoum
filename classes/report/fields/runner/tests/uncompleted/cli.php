<?php

namespace atoum\atoum\report\fields\runner\tests\uncompleted;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\tests\uncompleted
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
            $uncompletedMethods = $this->runner->getScore()->getUncompletedMethods();

            $sizeOfUncompletedMethod = count($uncompletedMethods);

            if ($sizeOfUncompletedMethod > 0) {
                $string .=
                    $this->titlePrompt .
                    sprintf(
                        $this->locale->_('%s:'),
                        $this->titleColorizer->colorize(sprintf($this->locale->__('There is %d uncompleted method', 'There are %d uncompleted methods', $sizeOfUncompletedMethod), $sizeOfUncompletedMethod))
                    ) .
                    PHP_EOL
                ;

                foreach ($uncompletedMethods as $uncompletedMethod) {
                    $string .=
                        $this->methodPrompt .
                        sprintf(
                            $this->locale->_('%s:'),
                            $this->methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $uncompletedMethod['class'], $uncompletedMethod['method'], $uncompletedMethod['exitCode']))
                        ) .
                        PHP_EOL
                    ;

                    $lines = explode(PHP_EOL, trim($uncompletedMethod['output']));

                    $string .= $this->outputPrompt . 'output(' . strlen($uncompletedMethod['output']) . ') "' . array_shift($lines);

                    foreach ($lines as $line) {
                        $string .= PHP_EOL . $this->outputPrompt . $line;
                    }

                    $string .= '"' . PHP_EOL;
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
