<?php

namespace atoum\atoum\asserters;

class phpFloat extends integer
{
    public function setWith(mixed $value): static
    {
        variable::setWith($value);

        if ($this->analyzer->isFloat($this->value) === true) {
            $this->pass();
        } else {
            $this->fail($this->_('%s is not a float', $this));
        }

        return $this;
    }

    public function isNearlyEqualTo(float $value, ?float $epsilon = null, ?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value !== $value) {
            // see http://www.floating-point-gui.de/errors/comparison/ for more informations
            $absValue = abs($value);
            $absCurrentValue = abs($this->value);
            $offset = abs($absCurrentValue - $absValue);
            $offsetIsNaN = is_nan($offset);

            if ($offsetIsNaN === false && $epsilon === null) {
                $epsilon = pow(10, - ini_get('precision'));
            }

            switch (true) {
                case $absCurrentValue == 0 && $absValue == 0:
                    return $this->isEqualTo($value);

                case $offsetIsNaN === true:
                case $offset / ($absCurrentValue + $absValue) >= $epsilon:
                case $absCurrentValue * $absValue == 0 && $offset >= pow($epsilon, 2):
                    $this->fail(($failMessage ?: $this->_('%s is not nearly equal to %s with epsilon %s', $this, $this->getTypeOf($value), $epsilon)) . PHP_EOL . $this->diff($value));
            }
        }

        $this->pass();

        return $this;
    }

    public function isZero(?string $failMessage = null): static
    {
        return $this->isEqualTo(0.0, $failMessage);
    }
}
