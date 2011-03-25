<?php

namespace mageekguy\atoum\tests\units\writers\std;

use \mageekguy\atoum;
use \mageekguy\atoum\writers\std;

require_once(__DIR__ . '/../../runner.php');

class out extends atoum\test
{
	public function test__construct()
	{
		$adapter = new atoum\test\adapter();
		$adapter->fopen = null;
		$adapter->fwrite = null;

		$stdout = new std\out($adapter);

		$this->assert
			->object($stdout->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testWrite()
	{
		$adapter = new atoum\test\adapter();
		$adapter->fopen = null;
		$adapter->fwrite = null;

		$stdout = new std\out($adapter);

		$this->assert
			->object($stdout->write($string = uniqid()))->isIdenticalTo($stdout)
			->adapter($adapter)->call('fwrite', array(null, $string))
			->object($stdout->write($string = (uniqid() . "\n")))->isIdenticalTo($stdout)
			->adapter($adapter)->call('fwrite', array(null, $string))
		;
	}
}

?>
