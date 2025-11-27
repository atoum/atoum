<?php

namespace atoum\atoum\asserters;

use atoum\atoum\asserters\adapter\call;
use atoum\atoum\asserters\adapter\exceptions;

class adapter extends call
{
    public function __get(string $property): mixed
    {
        switch (strtolower($property)) {
            case 'withanyarguments':
            case 'withoutanyargument':
                return $this->{$property}();

            default:
                return parent::__get($property);
        }
    }

    public function call(string $function): static
    {
        return $this->setFunction($function);
    }

    public function withArguments(mixed ...$arguments): static
    {
        return $this->setArguments($arguments);
    }

    public function withIdenticalArguments(mixed ...$arguments): static
    {
        return $this->setIdenticalArguments($arguments);
    }

    public function withAtLeastArguments(array $arguments): static
    {
        return $this->setArguments($arguments);
    }

    public function withAtLeastIdenticalArguments(array $arguments): static
    {
        return $this->setIdenticalArguments($arguments);
    }

    public function withAnyArguments(): static
    {
        return $this->unsetArguments();
    }

    public function withoutAnyArgument(): static
    {
        return $this->withAtLeastArguments([]);
    }

    public function verify(callable $verify): static
    {
        return $this->setVerify($verify);
    }

    protected function adapterIsSet(): static
    {
        try {
            return parent::adapterIsSet();
        } catch (call\exceptions\logic $exception) {
            throw new exceptions\logic('Adapter is undefined');
        }
    }

    protected function callIsSet(): static
    {
        try {
            return parent::callIsSet();
        } catch (call\exceptions\logic $exception) {
            throw new exceptions\logic('Call is undefined');
        }
    }
}
