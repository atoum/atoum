<?php

namespace atoum\atoum\asserters;

class integer extends variable
{
    public function __call(string $method, array $arguments): mixed
    {
        $assertion = null;

        switch ($method) {
            case '<=':
                $assertion = 'isLessThanOrEqualTo';
                break;

            case '<':
                $assertion = 'isLessThan';
                break;

            case '>=':
                $assertion = 'isGreaterThanOrEqualTo';
                break;

            case '>':
                $assertion = 'isGreaterThan';
                break;

            default:
                return parent::__call($method, $arguments);
        }

        return call_user_func_array([$this, $assertion], $arguments);
    }

    public function __get(string $property): mixed
    {
        switch (strtolower($property)) {
            case 'iszero':
                return $this->isZero();

            default:
                return parent::__get($property);
        }
    }

    public function setWith(mixed $value): static
    {
        parent::setWith($value);

        if ($this->analyzer->isInteger($this->value) === true) {
            $this->pass();
        } else {
            $this->fail($this->_('%s is not an integer', $this));
        }

        return $this;
    }

    public function isZero(?string $failMessage = null): static
    {
        return $this->isEqualTo(0, $failMessage);
    }

    public function isGreaterThan(int|float $value, ?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value > $value) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not greater than %s', $this, $this->getTypeOf($value)));
        }

        return $this;
    }

    public function isGreaterThanOrEqualTo(int|float $value, ?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value >= $value) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not greater than or equal to %s', $this, $this->getTypeOf($value)));
        }

        return $this;
    }

    public function isLessThan(int|float $value, ?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value < $value) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not less than %s', $this, $this->getTypeOf($value)));
        }

        return $this;
    }

    public function isLessThanOrEqualTo(int|float $value, ?string $failMessage = null): static
    {
        if ($this->valueIsSet()->value <= $value) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not less than or equal to %s', $this, $this->getTypeOf($value)));
        }

        return $this;
    }
}
