<?php

namespace atoum\atoum\report\fields\runner\exceptions;

use atoum\atoum\cli\colorizer;
use atoum\atoum\cli\prompt;
use atoum\atoum\report;

class cli extends report\fields\runner\exceptions
{
    protected ?prompt $titlePrompt = null;
    protected ?colorizer $titleColorizer = null;
    protected ?prompt $methodPrompt = null;
    protected ?colorizer $methodColorizer = null;
    protected ?prompt $exceptionPrompt = null;
    protected ?colorizer $exceptionColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setTitlePrompt()
            ->setTitleColorizer()
            ->setMethodPrompt()
            ->setMethodColorizer()
            ->setExceptionPrompt()
            ->setExceptionColorizer()
        ;
    }

    public function __toString(): string
    {
        $string = '';

        if ($this->runner !== null) {
            $exceptions = $this->runner->getScore()->getExceptions();

            $sizeOfErrors = count($exceptions);

            if ($sizeOfErrors > 0) {
                $string .=
                    $this->titlePrompt .
                    sprintf(
                        $this->locale->_('%s:'),
                        $this->colorizeTitle(sprintf($this->locale->__('There is %d exception', 'There are %d exceptions', $sizeOfErrors), $sizeOfErrors))
                    ) .
                    PHP_EOL
                ;

                $class = null;
                $method = null;

                foreach ($exceptions as $exception) {
                    if ($exception['class'] !== $class || $exception['method'] !== $method) {
                        $string .=
                            $this->methodPrompt .
                            sprintf(
                                $this->locale->_('%s:'),
                                $this->colorizeMethod($exception['class'] . '::' . $exception['method'] . '()')
                            ) .
                            PHP_EOL
                        ;

                        $class = $exception['class'];
                        $method = $exception['method'];
                    }

                    $string .=
                        $this->exceptionPrompt .
                        sprintf(
                            $this->locale->_('%s:'),
                            $this->colorizeException($this->locale->_('An exception has been thrown in file %s on line %d', $exception['file'], $exception['line']))
                        ) .
                        PHP_EOL
                    ;

                    foreach (explode(PHP_EOL, rtrim($exception['value'])) as $line) {
                        $string .= $this->exceptionPrompt . $line . PHP_EOL;
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

    public function setExceptionPrompt(?prompt $prompt = null): static
    {
        $this->exceptionPrompt = $prompt ?: new prompt();

        return $this;
    }

    public function getExceptionPrompt(): prompt
    {
        return $this->exceptionPrompt;
    }

    public function setExceptionColorizer(?colorizer $colorizer = null): static
    {
        $this->exceptionColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getExceptionColorizer(): colorizer
    {
        return $this->exceptionColorizer;
    }

    private function colorizeTitle(string $title): string
    {
        return $this->titleColorizer === null ? $title : $this->titleColorizer->colorize($title);
    }

    private function colorizeMethod(string $method): string
    {
        return $this->methodColorizer === null ? $method : $this->methodColorizer->colorize($method);
    }

    private function colorizeException(string $exception): string
    {
        return $this->exceptionColorizer === null ? $exception : $this->exceptionColorizer->colorize($exception);
    }
}
