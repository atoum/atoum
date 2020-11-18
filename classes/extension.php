<?php

namespace atoum\atoum;

interface extension extends observer
{
    public function setRunner(runner $runner);
    public function setTest(test $test);
}
