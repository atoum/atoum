<?php

namespace atoum\atoum\asserters;

use atoum\atoum\asserters\adapter\call;
use atoum\atoum\exceptions;
use atoum\atoum\php;
use atoum\atoum\test;

class phpFunction extends adapter\call
{
    public function setWithTest(test $test): static
    {
        parent::setWithTest($test);

        $function = $this->call->getFunction();

        if ($function !== null) {
            $this->setWith($function);
        }

        return $this;
    }

    public function setWith(mixed $function): static
    {
        return parent::setWith(clone php\mocker::getAdapter())->setFunction($function);
    }

    public function wasCalled(): static
    {
        return $this->unsetArguments();
    }

    public function wasCalledWithArguments(mixed ...$arguments): static
    {
        return $this->setArguments($arguments);
    }

    public function wasCalledWithIdenticalArguments(mixed ...$arguments): static
    {
        return $this->setIdenticalArguments($arguments);
    }

    public function wasCalledWithAnyArguments(): static
    {
        return $this->unsetArguments();
    }

    public function wasCalledWithoutAnyArgument(): static
    {
        return $this->setArguments([]);
    }

    protected function setFunction($function): static
    {
        if ($function !== null) {
            $function = (string) $function;
        }

        if ($this->test !== null && $function !== null) {
            $lastNamespaceSeparator = strrpos($function, '\\');

            if ($lastNamespaceSeparator !== false) {
                $function = substr($function, $lastNamespaceSeparator + 1);
            }

            $function = $this->test->getTestedClassNamespace() . '\\' . $function;
        }

        return parent::setFunction($function);
    }

    protected function adapterIsSet(): static
    {
        try {
            return parent::adapterIsSet();
        } catch (call\exceptions\logic $exception) {
            throw new exceptions\logic('Function is undefined');
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
