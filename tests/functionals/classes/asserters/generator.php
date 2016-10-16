<?php

namespace mageekguy\atoum\tests\functionals\asserters;

use mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\tests\functionals\test\functional
{
    /**
     * @php >= 7.0
     */
    public function testUsage()
    {
        if (version_compare(PHP_VERSION, '7.0') >= 0) {
            $generator = eval(<<<'PHP'
return function() {
    for ($i=0; $i<3; $i++) {
        yield ($i+1);
    }

    return 42;
};
PHP
            );
        }

        $this
            ->generator($generator())
                ->yields->variable->isEqualTo(1)
                ->yields->variable->isEqualTo(2)
                ->yields->variable->isEqualTo(3)
                ->yields->variable->isNull()
                ->returns->variable->isEqualTo(42)
            ->generator($generator())
                ->size->isEqualTo(3)
        ;
    }

    /**
     * @php >= 7.0
     */
    public function testUsageComplect()
    {
        if (version_compare(PHP_VERSION, '7.0') >= 0) {
            $generator = eval(<<<'PHP'
return function() {
    yield [
        "1", "2", "3"
    ];

    yield 0;
    yield 0;

    yield ["a" => 1, "b" => 2, "c" => 3];

    return 42;
};
PHP
            );
        }

        $this
            ->generator($generator())
                ->yields
                    ->array
                        ->isEqualTo(["1", "2", "3"])
                        ->contains("1")
                        ->string[0]->isEqualTo(1)
                ->yields
                    ->integer
                        ->isZero()
                ->yields
                    ->integer
                        ->isZero
                ->yields
                    ->array
                        ->keys->isEqualTo(['a', 'b', 'c'])
                ->yields
                    ->variable->isNull
                ->returns
                    ->variable
                        ->isEqualTo(42)
        ;
    }
}
