<?php

namespace atoum\atoum\cli;

use atoum\atoum;
use atoum\atoum\writer;

class colorizer implements writer\decorator
{
    protected ?atoum\cli $cli = null;
    protected ?string $pattern = null;
    protected ?string $foreground = null;
    protected ?string $background = null;

    public function __construct(?string $foreground = null, ?string $background = null, ?atoum\cli $cli = null)
    {
        if ($foreground !== null) {
            $this->setForeground($foreground);
        }

        if ($background !== null) {
            $this->setBackground($background);
        }

        $this->setCli($cli);
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

    public function setPattern(string $pattern): static
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function setForeground(string $foreground): static
    {
        $this->foreground = (string) $foreground;

        return $this;
    }

    public function getForeground(): ?string
    {
        return $this->foreground;
    }

    public function setBackground(string $background): static
    {
        $this->background = (string) $background;

        return $this;
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function colorize(?string $string): string
    {
        if ($string === null) {
            return '';
        }

        if ($this->cli->isTerminal() === true && ($this->foreground !== null || $this->background !== null)) {
            $pattern = $this->pattern ?: '/^(.*)$/';

            $replace = '\1';

            if ($this->background !== null || $this->foreground !== null) {
                if ($this->background !== null) {
                    $replace = "\033[" . $this->background . 'm' . $replace;
                }

                if ($this->foreground !== null) {
                    $replace = "\033[" . $this->foreground . 'm' . $replace;
                }

                $replace .= "\033[0m";
            }

            $string = preg_replace($pattern, $replace, $string);
        }

        return $string;
    }

    public function decorate(string $string): string
    {
        return $this->colorize($string);
    }
}
