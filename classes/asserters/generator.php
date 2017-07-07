<?php

namespace mageekguy\atoum\asserters;

use mageekguy\atoum\asserters\generator\asserterProxy;
use mageekguy\atoum\exceptions
;

class generator extends iterator
{
    protected $lastYieldValue;
    protected $lastRetunedValue;

    public function __get($property)
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

    public function setWith($value, $checkType = true)
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
