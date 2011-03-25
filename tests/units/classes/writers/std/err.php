<?php

namespace mageekguy\atoum\tests\units\writers\std;

use \mageekguy\atoum;
use \mageekguy\atoum\writers\std;

require_once(__DIR__ . '/../../../runner.php');

class err extends atoum\test
{
	public function test__construct()
	{
		$adapter = new atoum\test\adapter();
		$adapter->fopen = null;
		$adapter->fwrite = null;

		$stderr = new std\err($adapter);

		$this->assert
			->object($stderr->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testWrite()
	{
		$adapter = new atoum\test\adapter();
		$adapter->fopen = null;
		$adapter->fwrite = null;

		$stderr = new std\err($adapter);

		$this->assert
			->object($stderr->write($string = uniqid()))->isIdenticalTo($stderr)
			->adapter($adapter)->call('fwrite', array(null, $string))
			->object($stderr->write($string = uniqid() . "\n"))->isIdenticalTo($stderr)
			->adapter($adapter)->call('fwrite', array(null, $string))
		;
	}
}

?>
