<?php

namespace mageekguy\atoum\tests\functionals\test\data\providers;

use mageekguy\atoum;

require_once __DIR__ . '/../../../../runner.php';

class mock extends atoum\tests\functionals\test\functional
{
    /** @tags feature feature-496 mock */
    public function testCloneMock(\stdClass $mock, \stdClass $otherMock)
    {
        $this
            ->object($mock)->isInstanceOf(\stdClass::class)
            ->mock($mock)
            ->object($otherMock)->isInstanceOf(\stdClass::class)
            ->mock($otherMock)
        ;
    }
}
