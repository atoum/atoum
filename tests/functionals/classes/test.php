<?php

namespace atoum\atoum\tests\functionals;

use atoum\atoum;
use atoum\atoum\attributes as Attributes;

require_once __DIR__ . '/../runner.php';

class test extends atoum\tests\functionals\test\functional
{
    public function setUp()
    {
        echo __METHOD__;
    }

    public function beforeTestMethod($method)
    {
        echo __METHOD__;
    }

    public function afterTestMethod($method)
    {
        echo __METHOD__;
    }

    public function tearDown()
    {
        echo __METHOD__;
    }

    #[Attributes\Tags('issue', 'issue-820')]
    public function testOutputFromBeforeAndAfterTestMethod()
    {
        $this->boolean(true)->isTrue();
    }
}
