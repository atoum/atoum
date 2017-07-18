<?php

namespace mageekguy\atoum\tests\functionals\test;

use mageekguy\atoum;

class functional extends atoum\test
{
    public function getTestNamespace()
    {
        return '#(?:^|\\\)tests?\\\functionals?\\\#i';
    }

    public function getTestedClassName()
    {
        return 'stdClass';
    }
}
