<?php

namespace mageekguy\atoum\cli\progressBar;

class dot
{
    const width = 60;
    const defaultCounterFormat = '[%s/%s]';

    protected $refresh = null;
    protected $iterations = 0;
    protected $currentIteration = 0;

    public function __construct($iterations = 0)
    {
        $this->iterations = $iterations;
    }

    public function reset()
    {
        $this->refresh = null;
        $this->currentIteration = 0;

        return $this;
    }

    public function setIterations($iterations)
    {
        $this->reset()->iterations = (int) $iterations;

        return $this;
    }

    public function __toString()
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

    public function refresh($value)
    {
        $this->refresh .= $value;

        return $this;
    }

    private static function formatCounter($iterations, $currentIteration)
    {
        return sprintf(sprintf(self::defaultCounterFormat, '%' . strlen($iterations) . 'd', '%d'), $currentIteration, $iterations);
    }
}
