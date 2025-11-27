<?php

namespace atoum\atoum\asserters;

use atoum\atoum;
use atoum\atoum\test\adapter\call\decorators;

class mock extends adapter
{
    public function setWith(mixed $mock): static
    {
        if ($mock instanceof atoum\mock\aggregator === false) {
            $this->fail($this->_('%s is not a mock', $this->getTypeOf($mock)));
        } else {
            parent::setWith($mock->getMockController());

            $this->call->setDecorator(new decorators\addClass($this->adapter->getMockClass()));
        }

        return $this;
    }

    public function receive(string $function): static
    {
        return $this->call($function);
    }

    public function wasCalled(?string $failMessage = null): static
    {
        if ($this->adapterIsSet()->adapter->getCallsNumber() > 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is not called', $this->adapter->getMockClass()));
        }

        return $this;
    }

    public function wasNotCalled(?string $failMessage = null): static
    {
        if ($this->adapterIsSet()->adapter->getCallsNumber() <= 0) {
            $this->pass();
        } else {
            $this->fail($failMessage ?: $this->_('%s is called', $this->adapter->getMockClass()));
        }

        return $this;
    }

    protected function adapterIsSet(): static
    {
        try {
            return parent::adapterIsSet();
        } catch (adapter\exceptions\logic $exception) {
            throw new mock\exceptions\logic('Mock is undefined');
        }
    }

    protected function callIsSet(): static
    {
        try {
            return parent::callIsSet();
        } catch (adapter\exceptions\logic $exception) {
            throw new mock\exceptions\logic('Call is undefined');
        }
    }
}
