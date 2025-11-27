<?php

namespace atoum\atoum\asserters\generator;

use ArrayAccess;
use atoum\atoum;
use atoum\atoum\asserter\definition;

class asserterProxy implements definition, ArrayAccess
{
    private atoum\asserters\generator $parent;

    private definition $proxiedAsserter;

    public function __construct(atoum\asserters\generator $parent, definition $proxiedAsserter)
    {
        $this->parent = $parent;
        $this->proxiedAsserter = $proxiedAsserter;
    }

    public function __get(string $property): mixed
    {
        switch (strtolower($property)) {
            case 'yields':
            case 'returns':
                return $this->parent->__get($property);
            default:
                return $this->proxyfyAsserter($this->proxiedAsserter->{$property});
        }
    }

    protected function proxyfyAsserter(definition $asserter): self
    {
        return new self($this->parent, $asserter);
    }

    public function __call(string $name, array $arguments): mixed
    {
        $return = call_user_func_array([$this->proxiedAsserter, $name], $arguments);

        if ($return instanceof definition) {
            return $this->proxyfyAsserter($return);
        }

        return $return;
    }

    public function setLocale(?atoum\locale $locale = null): static
    {
        return $this->proxiedAsserter->setLocale($locale);
    }

    public function setGenerator(?atoum\asserter\generator $generator = null): static
    {
        $this->proxiedAsserter->setGenerator($generator);

        return $this;
    }

    public function setWithTest(atoum\test $test): static
    {
        $this->proxiedAsserter->setWithTest($test);

        return $this;
    }

    public function setWith(mixed $mixed): static
    {
        $this->proxiedAsserter->setWith($mixed);

        return $this;
    }

    public function setWithArguments(array $arguments): static
    {
        $this->proxiedAsserter->setWithArguments($arguments);

        return $this;
    }

    protected function checkIfProxySupportsArrayAccess()
    {
        if (!$this->proxiedAsserter instanceof ArrayAccess) {
            throw new \Exception(sprintf('Cannot use object of type %s as array', get_class($this->proxiedAsserter)));
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists(mixed $offset): bool
    {
        $this->checkIfProxySupportsArrayAccess();

        return $this->proxiedAsserter->offsetExists($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet(mixed $offset): mixed
    {
        $this->checkIfProxySupportsArrayAccess();

        $value = $this->proxiedAsserter->offsetGet($offset);

        return $value instanceof definition ? $this->proxyfyAsserter($value) : $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->checkIfProxySupportsArrayAccess();

        $this->proxiedAsserter->offsetSet($offset, $value);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset(mixed $offset): void
    {
        $this->checkIfProxySupportsArrayAccess();

        $this->proxiedAsserter->offsetUnset($offset);
    }
}
