<?php

namespace mageekguy\atoum\test;

use mageekguy\atoum;

abstract class engine
{
    abstract public function isAsynchronous();
    abstract public function run(atoum\test $test);
    abstract public function getScore();
}
