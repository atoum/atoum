<?php

namespace atoum\atoum\cli;

use atoum\atoum;

class progressBar
{
    public const width = 60;
    public const defaultProgressBarFormat = '[%s]';
    public const defaultCounterFormat = '[%s/%s]';

    protected ?atoum\cli $cli = null;
    protected ?string $refresh = null;
    protected ?string $progressBar = null;
    protected ?string $progressBarFormat = null;
    protected ?string $counter = null;
    protected ?string $counterFormat = null;
    protected int $iterations = 0;
    protected int $currentIteration = 0;

    public function __construct(int $iterations = 0, ?atoum\cli $cli = null)
    {
        $this->iterations = $iterations;
        $this->progressBarFormat = self::defaultProgressBarFormat;
        $this->counterFormat = self::defaultCounterFormat;

        $this->setCli($cli ?: new atoum\cli());
    }

    public function reset(): static
    {
        $this->refresh = null;
        $this->iterations = 0;
        $this->currentIteration = 0;
        $this->progressBar = null;
        $this->counter = null;

        return $this;
    }

    public function setIterations(int $iterations): static
    {
        $this->reset()->iterations = (int) $iterations;

        return $this;
    }

    public function setCli(atoum\cli $cli): static
    {
        $this->cli = $cli;

        return $this;
    }

    public function getCli(): atoum\cli
    {
        return $this->cli;
    }

    public function __toString(): string
    {
        $string = '';

        if ($this->progressBar === null && $this->counter === null) {
            $this->progressBar = sprintf($this->progressBarFormat, ($this->iterations > self::width ? str_repeat('.', self::width - 1) . '>' : str_pad(str_repeat('.', $this->iterations), self::width, '_', STR_PAD_RIGHT)));

            $this->counter = sprintf($this->counterFormat, sprintf('%' . strlen((string) $this->iterations) . 'd', $this->currentIteration), $this->iterations);

            $string .= $this->progressBar . $this->counter;
        }

        if ($this->refresh !== null) {
            $refreshLength = strlen($this->refresh);

            $this->currentIteration += $refreshLength;

            if ($this->cli->isTerminal() === false) {
                $this->progressBar = substr($this->progressBar, 0, $this->currentIteration) . $this->refresh . substr($this->progressBar, $this->currentIteration + 1);
                $string .= PHP_EOL . $this->progressBar;
            } else {
                $string .= str_repeat("\010", (strlen($this->progressBar) - $refreshLength) + strlen($this->counter));
                $this->progressBar = $this->refresh . substr($this->progressBar, $refreshLength + 1);
                $string .= $this->progressBar;
            }

            $this->counter = sprintf($this->counterFormat, sprintf('%' . strlen((string) $this->iterations) . 'd', $this->currentIteration), $this->iterations);

            $string .= $this->counter;

            if ($this->iterations > self::width && $this->iterations - $this->currentIteration && $this->currentIteration % (self::width - 1) == 0) {
                $this->progressBar = '[' . (($this->iterations - $this->currentIteration) > (self::width - 1) ? str_repeat('.', self::width - 1) . '>' : str_pad(str_repeat('.', $this->iterations - $this->currentIteration), self::width, '_', STR_PAD_RIGHT)) . ']';
                $this->counter = '';

                $string .= PHP_EOL . $this->progressBar;
            }

            $this->refresh = null;
        }

        return $string;
    }

    public function refresh($value)
    {
        if ($this->iterations > 0 && $this->currentIteration < $this->iterations) {
            $this->refresh .= $value;
        }

        return $this;
    }
}
