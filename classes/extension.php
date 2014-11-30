<?php

namespace mageekguy\atoum;

interface extension extends observer
{
	public function setRunner(runner $runner);
	public function setTest(test $test);
}
