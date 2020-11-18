<?php

namespace atoum\atoum\tests\units\asserters;

use atoum\atoum;
use atoum\atoum\asserter;
use atoum\atoum\asserters;

require_once __DIR__ . '/../../runner.php';

class testedClass extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->isSubclassOf(atoum\asserters\phpClass::class);
    }

    public function testSetWith()
    {
        $this
            ->if($asserter = new asserters\testedClass(new asserter\generator()))
            ->then
                ->exception(function () use ($asserter) {
                    $asserter->setWith(uniqid());
                })
                    ->isInstanceOf(atoum\exceptions\logic\badMethodCall::class)
                    ->hasMessage('Unable to call method ' . get_class($asserter) . '::setWith()')
        ;
    }
}
