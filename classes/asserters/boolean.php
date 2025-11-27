<?php

namespace atoum\atoum\asserters;

class boolean extends variable
{
    public function __get(string $property): mixed
    {
        switch (strtolower($property)) {
            case 'isfalse':
            case 'istrue':
                return $this->{$property}();

            default:
                return parent::__get($property);
        }
    }

    public function setWith(mixed $value): static
    {
        parent::setWith($value);

        if ($this->analyzer->isBoolean($this->value) === true) {
            $this->pass();
        } else {
            $this->fail($this->_('%s is not a boolean', $this));
        }

        return $this;
    }

    public function isTrue(?string $failMessage = null): static
    {
        return $this->isEqualTo(true, $failMessage ?: $this->_('%s is not true', $this));
    }

    public function isFalse(?string $failMessage = null): static
    {
        return $this->isEqualTo(false, $failMessage ?: $this->_('%s is not false', $this));
    }
}
