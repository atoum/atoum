<?php

namespace mageekguy\atoum\test\phpunit\mock\definition;

use mageekguy\atoum\asserters;
use mageekguy\atoum\mock\controller;

interface call
{
	public function define(controller $controller, $method);
}