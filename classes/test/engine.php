<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum
;

abstract class engine
{
	public abstract function isAsynchronous();
	public abstract function run(atoum\test $test);
	public abstract function getScore();
}
