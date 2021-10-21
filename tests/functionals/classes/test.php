<?php

namespace atoum\atoum\tests\functionals;

use atoum\atoum;

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

    /** @tags issue issue-820 */
    public function testOutputFromBeforeAndAfterTestMethod()
    {
        $this->boolean(true)->isTrue();
    }
}
