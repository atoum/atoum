<?php

namespace mageekguy\atoum\tests\units\writers;

use \mageekguy\atoum;
use \mageekguy\atoum\writers;

require_once(__DIR__ . '/../../runner.php');

class stdout extends atoum\test
{
	public function test__construct()
	{
		$stdout = new writers\stdout();

		$this->assert
			->object($stdout)->isInstanceOf('\mageekguy\atoum\adapter\aggregator')
			->object($stdout->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$adapter = new atoum\adapter();

		$stdout = new writers\stdout($adapter);

		$this->assert
			->object($stdout->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testWrite()
	{
		$adapter = new atoum\adapter();
		$adapter->fwrite = function() {};

		if (defined('STDOUT') === false)
		{
			define('STDOUT', uniqid());
		}

		$stdout = new writers\stdout($adapter);

		$this->assert
			->object($stdout->write($string = uniqid()))->isIdenticalTo($stdout)
			->adapter($adapter)->call('fwrite', array(STDOUT, $string))
			->object($stdout->write($string = (uniqid() . "\n")))->isIdenticalTo($stdout)
			->adapter($adapter)->call('fwrite', array(STDOUT, $string))
		;
	}
}

?>
