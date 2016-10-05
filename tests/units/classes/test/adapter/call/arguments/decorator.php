<?php

namespace mageekguy\atoum\tests\units\test\adapter\call\arguments;

require __DIR__ . '/../../../../../runner.php';

use mageekguy\atoum;
use mageekguy\atoum\mock;
use mageekguy\atoum\test\adapter\call\arguments\decorator as testedClass;

class decorator extends atoum\test
{
    public function testDecorate()
    {
        $this
            ->if($decorator = new testedClass())
            ->then
                ->string($decorator->decorate())->isEmpty()
                ->string($decorator->decorate(null))->isEmpty()
                ->string($decorator->decorate([]))->isEmpty()
                ->string($decorator->decorate([1]))->isEqualTo('integer(1)')
                ->string($decorator->decorate([1, 2]))->isEqualTo('integer(1), integer(2)')
                ->string($decorator->decorate([1.0]))->isEqualTo('float(1)')
                ->string($decorator->decorate([1.0, 2.1]))->isEqualTo('float(' . 1.0 . '), float(' . 2.1 . ')')
                ->string($decorator->decorate([true]))->isEqualTo('TRUE')
                ->string($decorator->decorate([false]))->isEqualTo('FALSE')
                ->string($decorator->decorate([false, true]))->isEqualTo('FALSE, TRUE')
                ->string($decorator->decorate([null]))->isEqualTo('NULL')
                ->string($decorator->decorate([$this]))->isEqualTo('object(' . __CLASS__ . ')')
            ->if($stream = mock\stream::get())
            ->and($stream->fopen = true)
            ->and($resource = fopen($stream, 'r'))
            ->and($dump = function () use ($resource) {
                ob_start();
                var_dump($resource);
                return ob_get_clean();
            })
            ->then
                ->string($decorator->decorate([$resource]))->isEqualTo($dump())
        ;
    }
}
