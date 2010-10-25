<?php

namespace mageekguy\atoum\tests\units\writers;

use \mageekguy\atoum;
use \mageekguy\atoum\writers;

require_once(__DIR__ . '/../../runner.php');

class stderr extends atoum\test
{
	public function test__construct()
	{
		$stdout = new writers\stdout();

		$this->assert
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

		if (defined('STDERR') === false)
		{
			define('STDERR', uniqid());
		}

		$stdout = new writers\stderr($adapter);

		$this->assert
			->object($stdout->write($string = uniqid()))->isIdenticalTo($stdout)
			->adapter($adapter)->call('fwrite', array(STDERR, $string . "\n"))
			->object($stdout->write($string = uniqid() . "\n"))->isIdenticalTo($stdout)
			->adapter($adapter)->call('fwrite', array(STDERR, $string))
		;
	}
}

?>
