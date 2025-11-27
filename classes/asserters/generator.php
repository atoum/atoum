<?php

namespace atoum\atoum\asserters;

use atoum\atoum\asserters\generator\asserterProxy;
use atoum\atoum\exceptions
;

class generator extends iterator
{
    protected mixed $lastYieldValue = null;
    protected mixed $lastRetunedValue = null;

    public function __get(string $property): mixed
    {
        switch (strtolower($property)) {
            case 'yields':
                $generator = $this->valueIsSet()->value;

                $this->lastYieldValue = $generator->current();

                $generator->next();

                return $this;
            case 'returns':
                $generator = $this->valueIsSet()->value;

                if (!method_exists($generator, 'getReturn')) {
                    throw new exceptions\logic("The returns asserter could only be used with PHP>=7.0");
                }

                $this->lastRetunedValue = $generator->getReturn();

                return $this;
            default:
                try {
                    $asserter = $this->getGenerator()->getAsserterInstance($property);

                    $setWithValue = (null !== $this->lastRetunedValue) ? $this->lastRetunedValue : $this->lastYieldValue;
                    $asserter->setWith($setWithValue);

                    return new asserterProxy($this, $asserter);
                } catch (exceptions\logic\invalidArgument $e) {
                    return parent::__get($property);
                }
        }
    }

    public function setWith(mixed $value, bool $checkType = true): static
    {
        parent::setWith($value, $checkType);

        if ($value instanceof \Generator) {
            $this->pass();
        } else {
            $this->fail($this->_('%s is not a generator', $this));
        }

        return $this;
    }
}
