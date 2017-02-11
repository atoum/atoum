<?php

namespace mageekguy\atoum\runner;

use mageekguy\atoum\runner;

interface visitor
{
	public function __toString();

	public function visit(runner $runner);
}