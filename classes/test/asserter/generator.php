<?php

namespace atoum\atoum\test\asserter;

use atoum\atoum;
use atoum\atoum\asserter;

class generator extends asserter\generator
{
    protected $test = null;

    public function __construct(atoum\test $test, ?asserter\resolver $resolver = null)
    {
        parent::__construct($test->getLocale(), $resolver);

        $this->test = $test;
    }

    public function __get(string $property): mixed
    {
        return $this->test->__get($property);
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->test->__call($method, $arguments);
    }

    public function setTest(atoum\test $test)
    {
        $this->test = $test;

        return $this->setLocale($test->getLocale());
    }

    public function getTest()
    {
        return $this->test;
    }

    public function getAsserterInstance(string $asserter, array $arguments = [], ?atoum\test $test = null): atoum\asserter
    {
        return parent::getAsserterInstance($asserter, $arguments, $test ?: $this->test);
    }
}
