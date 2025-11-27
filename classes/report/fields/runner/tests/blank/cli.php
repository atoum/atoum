<?php

namespace atoum\atoum\report\fields\runner\tests\blank;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\tests\blank
{
    protected ?prompt $titlePrompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?prompt $methodPrompt = null;
    protected ?colorizer $methodColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setTitlePrompt()
            ->setTitleColorizer()
            ->setMethodPrompt()
            ->setMethodColorizer()
        ;
    }

    public function __toString(): string
    {
        $string = '';

        if ($this->runner !== null) {
            $voidMethods = $this->runner->getScore()->getVoidMethods();

            $sizeOfVoidMethod = count($voidMethods);

            if ($sizeOfVoidMethod > 0) {
                $string .=
                    $this->titlePrompt .
                    sprintf(
                        $this->locale->_('%s:'),
                        $this->titleColorizer->colorize(sprintf($this->locale->__('There is %d void method', 'There are %d void methods', $sizeOfVoidMethod), $sizeOfVoidMethod))
                    ) .
                    PHP_EOL
                ;

                foreach ($voidMethods as $voidMethod) {
                    $string .= $this->methodPrompt . $this->methodColorizer->colorize(sprintf('%s::%s()', $voidMethod['class'], $voidMethod['method'])) . PHP_EOL;
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
}
