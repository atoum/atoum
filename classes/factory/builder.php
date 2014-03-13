<?php

namespace mageekguy\atoum\factory;

use
	atoum\test
;

interface builder
{
	public function build(\reflectionClass $class, & $instance = null);
	public function get();
	public function addToAssertionManager(test\assertion\manager $assertionManager, $factoryName, $defaultHandler);
}
