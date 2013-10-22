<?php

namespace atoum\mock;

use
	atoum\mock
;

interface aggregator
{
	public function getMockController();
	public function setMockController(mock\controller $mockController);
	public function resetMockController();
	public static function getMockedMethods();
}
