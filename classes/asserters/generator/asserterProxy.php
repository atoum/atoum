<?php

namespace mageekguy\atoum\asserters\generator;

use ArrayAccess;
use mageekguy\atoum;
use mageekguy\atoum\asserter\definition;

class asserterProxy implements definition, ArrayAccess
{
    private $parent;

    private $proxiedAsserter;

    public function __construct(atoum\asserters\generator $parent, definition $proxiedAsserter)
    {
        $this->parent = $parent;
        $this->proxiedAsserter = $proxiedAsserter;
    }

    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'yields':
            case 'returns':
                return $this->parent->__get($property);
            default:
                return $this->proxyfyAsserter($this->proxiedAsserter->{$property});
        }
    }

    protected function proxyfyAsserter(definition $asserter)
    {
        return new self($this->parent, $asserter);
    }

    public function __call($name, $arguments)
    {
        $return = call_user_func_array([$this->proxiedAsserter, $name], $arguments);

        if ($return instanceof definition) {
            return $this->proxyfyAsserter($return);
        }

        return $return;
    }

    public function setLocale(atoum\locale $locale = null)
    {
        return $this->proxiedAsserter->setLocale($locale);
    }

    public function setGenerator(atoum\asserter\generator $generator = null)
    {
        return $this->setGenerator($generator);
    }

    public function setWithTest(atoum\test $test)
    {
        return $this->setWithTest($test);
    }

    public function setWith($mixed)
    {
        return $this->setWith($mixed);
    }

    public function setWithArguments(array $arguments)
    {
        return $this->setWithArguments($arguments);
    }

    protected function checkIfProxySupportsArrayAccess()
    {
        if (!$this->proxiedAsserter instanceof ArrayAccess) {
            throw new \Exception(sprintf('Cannot use object of type %s as array', get_class($this->proxiedAsserter)));
        }
    }

    public function offsetExists($offset)
    {
        $this->checkIfProxySupportsArrayAccess();
        return $this->proxyfyAsserter($this->proxiedAsserter->offsetExists($offset));
    }

    public function offsetGet($offset)
    {
        $this->checkIfProxySupportsArrayAccess();
        return $this->proxyfyAsserter($this->proxiedAsserter->offsetGet($offset));
    }

    public function offsetSet($offset, $value)
    {
        $this->checkIfProxySupportsArrayAccess();
        return $this->proxyfyAsserter($this->proxiedAsserter->offsetSet($offset, $value));
    }

    public function offsetUnset($offset)
    {
        $this->checkIfProxySupportsArrayAccess();
        return $this->proxyfyAsserter($this->proxiedAsserter->offsetUnset($offset));
    }
}
