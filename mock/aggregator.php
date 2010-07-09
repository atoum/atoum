<?php

namespace mageekguy\tests\unit\mock;

use \mageekguy\tests\unit\mock;

interface aggregator
{
	public function getMockController();
	public function setMockController(mock\controller $mockController);
	public function resetMockController();
}

?>
