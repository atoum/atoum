<?php

namespace atoum\atoum\tests\units\writer\decorators;

require_once __DIR__ . '/../../../runner.php';

use atoum\atoum;
use atoum\atoum\writer\decorators\trim as testedClass;

class trim extends atoum
{
    public function testDecorate()
    {
        $this
            ->if($decorator = new testedClass())
            ->then
                ->string($decorator->decorate($message = uniqid()))->isEqualTo($message)
                ->string($decorator->decorate(($message = uniqid()) . PHP_EOL))->isEqualTo($message)
                ->string($decorator->decorate(' ' . ($message = uniqid()) . PHP_EOL . PHP_EOL))->isEqualTo($message)
        ;
    }
}
