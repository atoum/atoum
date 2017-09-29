<?php

namespace mageekguy\atoum\asserters;

use mageekguy\atoum\asserters\adapter\call;
use mageekguy\atoum\asserters\adapter\exceptions;

class adapter extends call
{
    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'withanyarguments':
            case 'withoutanyargument':
                return $this->{$property}();

            default:
                return parent::__get($property);
        }
    }

    public function call($function)
    {
        return $this->setFunction($function);
    }

    public function withArguments(...$arguments)
    {
        return $this->setArguments($arguments);
    }

    public function withIdenticalArguments(...$arguments)
    {
        return $this->setIdenticalArguments($arguments);
    }

    public function withAtLeastArguments(array $arguments)
    {
        return $this->setArguments($arguments);
    }

    public function withAtLeastIdenticalArguments(array $arguments)
    {
        return $this->setIdenticalArguments($arguments);
    }

    public function withAnyArguments()
    {
        return $this->unsetArguments();
    }

    public function withoutAnyArgument()
    {
        return $this->withAtLeastArguments([]);
    }

    public function verify(callable $verify)
    {
        return $this->setVerify($verify);
    }

    protected function adapterIsSet()
    {
        try {
            return parent::adapterIsSet();
        } catch (call\exceptions\logic $exception) {
            throw new exceptions\logic('Adapter is undefined');
        }
    }

    protected function callIsSet()
    {
        try {
            return parent::callIsSet();
        } catch (call\exceptions\logic $exception) {
            throw new exceptions\logic('Call is undefined');
        }
    }
}
