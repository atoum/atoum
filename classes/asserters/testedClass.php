<?php

namespace atoum\atoum\asserters;

use atoum\atoum;
use atoum\atoum\exceptions;

class testedClass extends phpClass
{
    public function setWith(mixed $class): static
    {
        throw new exceptions\logic\badMethodCall('Unable to call method ' . __METHOD__ . '()');
    }

    public function setWithTest(atoum\test $test): static
    {
        parent::setWith($test->getTestedClassName());

        return parent::setWithTest($test);
    }
}
