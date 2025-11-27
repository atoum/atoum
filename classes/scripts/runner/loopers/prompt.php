<?php

namespace atoum\atoum\scripts\runner\loopers;

use atoum\atoum;
use atoum\atoum\script;
use atoum\atoum\scripts\runner\looper;
use atoum\atoum\writers;

class prompt implements looper
{
    private ?atoum\writer $writer;
    private ?script\prompt $prompt;
    private ?atoum\locale $locale;
    private ?atoum\cli $cli;

    public function __construct(?script\prompt $prompt = null, ?atoum\writer $writer = null, ?atoum\cli $cli = null, ?atoum\locale $locale = null)
    {
        $this
            ->setCli($cli)
            ->setOutputWriter($writer)
            ->setPrompt($prompt)
            ->setLocale($locale)
        ;
    }

    public function setCli(?atoum\cli $cli = null): static
    {
        $this->cli = $cli ?: new atoum\cli();

        return $this;
    }

    public function getCli(): atoum\cli
    {
        return $this->cli;
    }

    public function setOutputWriter(?atoum\writer $writer = null): static
    {
        $this->writer = $writer ?: new writers\std\out($this->cli);

        return $this;
    }

    public function getOutputWriter(): atoum\writer
    {
        return $this->writer;
    }

    public function setPrompt(?script\prompt $prompt = null): static
    {
        if ($prompt === null) {
            $prompt = new script\prompt();
        }

        $this->prompt = $prompt->setOutputWriter($this->writer);

        return $this;
    }

    public function getPrompt(): script\prompt
    {
        return $this->prompt;
    }

    public function setLocale(?atoum\locale $locale = null): static
    {
        $this->locale = $locale ?: new atoum\locale();

        return $this;
    }

    public function getLocale(): atoum\locale
    {
        return $this->locale;
    }

    public function runAgain(): bool
    {
        return ($this->prompt($this->locale->_('Press <Enter> to reexecute, press any other key and <Enter> to stop...')) == '');
    }

    private function prompt(string $message): string
    {
        return trim($this->prompt->ask(rtrim($message)));
    }
}
