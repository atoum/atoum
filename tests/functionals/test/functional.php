<?php

namespace atoum\atoum\tests\functionals\test;

use atoum\atoum;

class functional extends atoum\test
{
    public function getTestNamespace(): string
    {
        return '#(?:^|\\\)tests?\\\functionals?\\\#i';
    }

    public function getTestedClassName(): ?string
    {
        return \stdClass::class;
    }
}
