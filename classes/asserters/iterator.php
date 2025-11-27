<?php

namespace atoum\atoum\asserters;

class iterator extends phpObject
{
    public function __get(string $asserter): mixed
    {
        switch (strtolower($asserter)) {
            case 'size':
                return $this->size();

            case 'isempty':
                return $this->isEmpty();

            case 'isnotempty':
                return $this->isNotEmpty();

            default:
                return parent::__get($asserter);
        }
    }

    public function setWith(mixed $value, bool $checkType = true): static
    {
        parent::setWith($value, $checkType);

        if ($checkType === true) {
            if (self::isIterator($this->value) === false) {
                $this->fail($this->getLocale()->_('%s is not an iterator', $this));
            } else {
                $this->pass();
            }
        }

        return $this;
    }

    public function hasSize(int $size, ?string $failMessage = null): static
    {
        if (($actual = iterator_count($this->valueIsSet()->value)) == $size) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->getLocale()->_('%s has size %d, expected size %d', $this, $actual, $size));
        }

        return $this;
    }

    public function isEmpty(?string $failMessage = null): static
    {
        if (($actual = iterator_count($this->valueIsSet()->value)) === 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->getLocale()->_('%s is not empty', $this, $actual));
        }

        return $this;
    }

    public function isNotEmpty(?string $failMessage = null): static
    {
        if (iterator_count($this->valueIsSet()->value) > 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is empty', $this));
        }

        return $this;
    }

    protected function size(): \atoum\atoum\asserters\integer
    {
        return $this->generator->__call('integer', [iterator_count($this->valueIsSet()->value)]);
    }

    protected static function isIterator(mixed $value): bool
    {
        return ($value instanceof \iterator);
    }
}
