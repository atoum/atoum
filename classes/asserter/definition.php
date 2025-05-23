<?php

namespace atoum\atoum\asserter;

use atoum\atoum;

interface definition
{
    public function setLocale(?atoum\locale $locale = null);
    public function setGenerator(?atoum\asserter\generator $generator = null);
    public function setWithTest(atoum\test $test);
    public function setWith($mixed);
    public function setWithArguments(array $arguments);
}
