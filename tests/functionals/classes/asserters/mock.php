<?php

namespace mageekguy\atoum\tests\functionals\asserters;

use mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class objectAMocker
{
    public function run($test) {

    }
}

class mock extends atoum\tests\functionals\test\functional
{
    public function testUsage()
    {
        $mock = new \mock\mageekguy\atoum\tests\functionals\asserters\objectAMocker();
        $mock->run('42', ['aaa']);
        //$mock->run('42', ['aaa']);
        $this
            ->mock($mock)
                ->call('run')
                ->onceAndCheckFirstCallArguments(function($arguments) {
                    $this
                        ->string($arguments[0])
                            ->isEqualTo('42')
                            ->hasLength(2)
                        ->array($arguments[1])
                            ->hasSize(1)
                    ;
                })

        ;
    }
}
