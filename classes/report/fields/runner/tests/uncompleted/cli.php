<?php

namespace mageekguy\atoum\report\fields\runner\tests\incompleted;

use mageekguy\atoum\cli\colorizer;
use mageekguy\atoum\cli\prompt;
use mageekguy\atoum\report;

class cli extends report\fields\runner\tests\incompleted
{
    protected $titlePrompt = null;
    protected $titleColorizer = null;
    protected $methodPrompt = null;
    protected $methodColorizer = null;
    protected $outputPrompt = null;
    protected $outputColorizer = null;

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

    public function __toString()
    {
        $string = '';

        if ($this->runner !== null) {
            $incompletedMethods = $this->runner->getScore()->getincompletedMethods();

            $sizeOfincompletedMethod = sizeof($incompletedMethods);

            if ($sizeOfincompletedMethod > 0) {
                $string .=
                    $this->titlePrompt .
                    sprintf(
                        $this->locale->_('%s:'),
                        $this->titleColorizer->colorize(sprintf($this->locale->__('There is %d incompleted method', 'There are %d incompleted methods', $sizeOfincompletedMethod), $sizeOfincompletedMethod))
                    ) .
                    PHP_EOL
                ;

                foreach ($incompletedMethods as $incompletedMethod) {
                    $string .=
                        $this->methodPrompt .
                        sprintf(
                            $this->locale->_('%s:'),
                            $this->methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $incompletedMethod['class'], $incompletedMethod['method'], $incompletedMethod['exitCode']))
                        ) .
                        PHP_EOL
                    ;

                    $lines = explode(PHP_EOL, trim($incompletedMethod['output']));

                    $string .= $this->outputPrompt . 'output(' . strlen($incompletedMethod['output']) . ') "' . array_shift($lines);

                    foreach ($lines as $line) {
                        $string .= PHP_EOL . $this->outputPrompt . $line;
                    }

                    $string .= '"' . PHP_EOL;
                }
            }
        }

        return $string;
    }

    public function setTitlePrompt(prompt $prompt = null)
    {
        $this->titlePrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getTitlePrompt()
    {
        return $this->titlePrompt;
    }

    public function setTitleColorizer(colorizer $colorizer = null)
    {
        $this->titleColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getTitleColorizer()
    {
        return $this->titleColorizer;
    }

    public function setMethodPrompt(prompt $prompt = null)
    {
        $this->methodPrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getMethodPrompt()
    {
        return $this->methodPrompt;
    }

    public function setMethodColorizer(colorizer $colorizer = null)
    {
        $this->methodColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getMethodColorizer()
    {
        return $this->methodColorizer;
    }

    public function setOutputPrompt(prompt $prompt = null)
    {
        $this->outputPrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getOutputPrompt()
    {
        return $this->outputPrompt;
    }

    public function setOutputColorizer(colorizer $colorizer = null)
    {
        $this->outputColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getOutputColorizer()
    {
        return $this->outputColorizer;
    }
}
