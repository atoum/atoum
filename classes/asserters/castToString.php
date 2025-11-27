<?php

namespace atoum\atoum\asserters;

class castToString extends phpString
{
    public function setWith(mixed $value, ?string $charlist = null, bool $checkType = true): static
    {
        parent::setWith($value, $charlist, false);

        if ($checkType === true) {
            if (self::isObject($value) === false) {
                $this->fail($this->_('%s is not an object', $this->getTypeOf($value)));
            } else {
                $this->pass();

                $this->value = (string) $this->value;
            }
        }

        return $this;
    }

    protected static function isObject(mixed $value): bool
    {
        return (is_object($value) === true);
    }
}
