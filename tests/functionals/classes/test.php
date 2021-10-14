<?php

namespace atoum\atoum\tests\functionals;

use atoum\atoum;

require_once __DIR__ . '/../runner.php';

class test extends atoum\tests\functionals\test\functional
{
    public function beforeTestMethod($method)
    {
        echo "start\n";
    }

    public function afterTestMethod($method)
    {
        echo "end\n";
    }

    /** @tags issue issue-820 */
    public function testOutputFromBeforeAndAfterTestMethod()
    {
        $this->boolean(true)->isTrue();
    }
}
