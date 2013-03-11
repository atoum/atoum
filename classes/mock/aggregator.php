<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum\mock
;

interface aggregator
{
	public function getMockController();
	public function setMockController(mock\controller $mockController);
	public function resetMockController();
	public static function getMockedMethods();
}
