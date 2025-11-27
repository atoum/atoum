<?php

namespace atoum\atoum\asserters;

use atoum\atoum\exceptions;

class dateInterval extends phpObject
{
    public function __toString(): string
    {
        return (static::isDateInterval($this->value) === false ? parent::__toString() : $this->format($this->value));
    }

    public function __get(string $asserter): mixed
    {
        switch (strtolower($asserter)) {
            case 'iszero':
                return $this->{$asserter}();

            default:
                return parent::__get($asserter);
        }
    }

    public function setWith(mixed $value, bool $checkType = true): static
    {
        parent::setWith($value, false);

        if ($checkType === true) {
            if (self::isDateInterval($this->value) === true) {
                $this->pass();
            } else {
                $this->fail($this->_('%s is not an instance of \\dateInterval', $this));
            }
        }

        return $this;
    }

    public function isGreaterThan(\dateInterval $interval, ?string $failMessage = null): static
    {
        list($date1, $date2) = $this->getDates($interval);

        if ($date1 > $date2) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('Interval %s is not greater than %s', $this, $this->format($interval)));
        }

        return $this;
    }

    public function isGreaterThanOrEqualTo(\dateInterval $interval, ?string $failMessage = null): static
    {
        list($date1, $date2) = $this->getDates($interval);

        if ($date1 >= $date2) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('Interval %s is not greater than or equal to %s', $this, $this->format($interval)));
        }

        return $this;
    }

    public function isLessThan(\dateInterval $interval, ?string $failMessage = null): static
    {
        list($date1, $date2) = $this->getDates($interval);

        if ($date1 < $date2) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('Interval %s is not less than %s', $this, $this->format($interval)));
        }

        return $this;
    }

    public function isLessThanOrEqualTo(\dateInterval $interval, ?string $failMessage = null): static
    {
        list($date1, $date2) = $this->getDates($interval);

        if ($date1 <= $date2) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('Interval %s is not less than or equal to %s', $this, $this->format($interval)));
        }

        return $this;
    }

    public function isEqualTo(mixed $interval, ?string $failMessage = null): static
    {
        list($date1, $date2) = $this->getDates($interval);

        if ($date1 == $date2) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('Interval %s is not equal to %s', $this, $this->format($interval)));
        }

        return $this;
    }

    public function isZero(?string $failMessage = null): static
    {
        return $this->isEqualTo(new \dateInterval('P0D'), $failMessage ?: $this->_('Interval %s is not equal to zero', $this));
    }

    protected function valueIsSet(string $message = 'Interval is undefined'): static
    {
        if (self::isDateInterval(parent::valueIsSet($message)->value) === false) {
            throw new exceptions\logic($message);
        }

        return $this;
    }

    protected function getDates(\dateInterval $interval): array
    {
        $this->valueIsSet();

        $date1 = new \dateTime();
        $date2 = clone $date1;

        return [$date1->add($this->value), $date2->add($interval)];
    }

    protected static function isDateInterval(mixed $value): bool
    {
        return ($value instanceof \dateInterval);
    }

    protected function format(\dateInterval $interval): string
    {
        return $interval->format($this->_('%Y/%M/%D %H:%I:%S'));
    }
}
