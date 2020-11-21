<?php

namespace atoum\atoum\tests\functionals\test;

use atoum\atoum;

class functional extends atoum\test
{
    public function getTestNamespace()
    {
        return '#(?:^|\\\)tests?\\\functionals?\\\#i';
    }

    public function getTestedClassName()
    {
        return \stdClass::class;
    }
}
