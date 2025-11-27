<?php

namespace atoum\atoum\report\fields\runner\tests\skipped;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report\fields\runner\tests\skipped;

class cli extends skipped
{
    protected ?prompt $titlePrompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?prompt $methodPrompt = null;
    protected ?colorizer $methodColorizer = null;
    protected ?prompt $messagePrompt = null;
    protected ?colorizer $messageColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setTitlePrompt()
            ->setTitleColorizer()
            ->setMethodPrompt()
            ->setMethodColorizer()
            ->setMessageColorizer()
        ;
    }

    public function __toString(): string
    {
        $string = '';

        if ($this->runner !== null) {
            $skippedMethods = $this->runner->getScore()->getSkippedMethods();

            $sizeOfSkippedMethod = count($skippedMethods);

            if ($sizeOfSkippedMethod > 0) {
                $string .=
                    $this->titlePrompt .
                    sprintf(
                        $this->locale->_('%s:'),
                        $this->titleColorizer->colorize(sprintf($this->locale->__('There is %d skipped method', 'There are %d skipped methods', $sizeOfSkippedMethod), $sizeOfSkippedMethod))
                    ) .
                    PHP_EOL
                ;

                foreach ($skippedMethods as $skippedMethod) {
                    $string .= $this->methodPrompt . $this->locale->_('%s: %s', $this->methodColorizer->colorize(sprintf('%s::%s()', $skippedMethod['class'], $skippedMethod['method'])), $this->messageColorizer->colorize($skippedMethod['message'])) . PHP_EOL;
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

    public function setMessageColorizer(?colorizer $colorizer = null): static
    {
        $this->messageColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getMessageColorizer(): colorizer
    {
        return $this->messageColorizer;
    }
}
