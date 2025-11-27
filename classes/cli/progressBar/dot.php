<?php

namespace atoum\atoum\cli\progressBar;

class dot
{
    public const width = 60;
    public const defaultCounterFormat = '[%s/%s]';

    protected ?string $refresh = null;
    protected int $iterations = 0;
    protected int $currentIteration = 0;

    public function __construct(int $iterations = 0)
    {
        $this->iterations = $iterations;
    }

    public function reset(): static
    {
        $this->refresh = null;
        $this->currentIteration = 0;

        return $this;
    }

    public function setIterations(int $iterations): static
    {
        $this->reset()->iterations = (int) $iterations;

        return $this;
    }

    public function __toString(): string
    {
        $string = '';

        if ($this->refresh !== '' && $this->currentIteration < $this->iterations) {
            foreach ((array) $this->refresh as $char) {
                $this->currentIteration++;

                $string .= $char;

                if ($this->currentIteration % self::width === 0) {
                    $string .= ' ' . self::formatCounter($this->iterations, $this->currentIteration) . PHP_EOL;
                }
            }

            if ($this->iterations > 0 && $this->currentIteration === $this->iterations && ($this->iterations % self::width) > 0) {
                $string .= str_repeat(' ', round(self::width - ($this->iterations % self::width))) . ' ' . self::formatCounter($this->iterations, $this->currentIteration) . PHP_EOL;
            }

            $this->refresh = '';
        }

        return $string;
    }

    public function refresh(string $value): static
    {
        $this->refresh .= $value;

        return $this;
    }

    private static function formatCounter(int $iterations, int $currentIteration): string
    {
        return sprintf(sprintf(self::defaultCounterFormat, '%' . strlen($iterations) . 'd', '%d'), $currentIteration, $iterations);
    }
}
