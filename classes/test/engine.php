<?php

namespace atoum\atoum\test;

use atoum\atoum;

abstract class engine
{
    abstract public function isAsynchronous();
    abstract public function run(atoum\test $test);
    abstract public function getScore();
}
