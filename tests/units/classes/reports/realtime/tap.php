<?php

namespace atoum\atoum\tests\units\reports\realtime;

use atoum\atoum;
use atoum\atoum\report\fields;
use atoum\atoum\reports\realtime\tap as testedClass;

require __DIR__ . '/../../../runner.php';

class tap extends atoum\test
{
    public function testClass()
    {
        $this->testedClass
            ->extends(atoum\reports\realtime::class)
        ;
    }

    public function test__construct()
    {
        $this
            ->define($planField = new fields\runner\tap\plan())
            ->define($eventField = new fields\test\event\tap())
            ->if($report = new testedClass())
            ->then
                ->object($report->getLocale())->isEqualTo(new atoum\locale())
                ->object($report->getAdapter())->isEqualTo(new atoum\adapter())
                ->array($report->getFields())->isEqualTo(
                    [
                        $planField,
                        $eventField
                    ]
                )
        ;
    }
}
