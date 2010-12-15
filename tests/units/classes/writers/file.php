<?php

namespace mageekguy\atoum\tests\units\writers;

use \mageekguy\atoum;
use \mageekguy\atoum\writers;

require_once(__DIR__ . '/../../runner.php');

class file extends atoum\test
{
	public function test__construct()
	{
		$file = new writers\file();

		$this->assert
			->object($file)->isInstanceOf('\mageekguy\atoum\adapter\aggregator')
			->object($file->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
			->string($file->getFilename())->isEqualTo('atoum.log')
		;

		$adapter = new atoum\adapter();
		$adapter->fopen = function() {};
		$adapter->fclose = function() {};
		
		$file = new writers\file(null, $adapter);
		
		$this->assert
			->object($file->getAdapter())->isIdenticalTo($adapter)
			->string($file->getFilename())->isEqualTo('atoum.log')
			->adapter($adapter)->call('fopen', array('atoum.log', 'w'))
			;
		
		$file = new writers\file('test.log');

		$this->assert
			->string($file->getFilename())->isEqualTo('test.log')
			;
	}

	public function test__destruct()
	{
		$id = uniqid();
		
		$adapter = new atoum\adapter();
		$adapter->fopen = function() use ($id) { return $id; };
		$adapter->fclose = function() {};
		
		$file = new writers\file(null, $adapter);
		
		unset($file);
		
		$this->assert
			->adapter($adapter)->call('fclose', array($id))
			;
	}
	
	public function testWrite()
	{
		$id = uniqid();
		
		$adapter = new atoum\adapter();
		$adapter->fopen = function() use ($id) { return $id; };
		$adapter->fclose = function() {};
		$adapter->fwrite = function() {};

		$file = new writers\file(null,$adapter);

		$this->assert
			->object($file->write($string = uniqid()))->isIdenticalTo($file)
			->adapter($adapter)->call('fwrite', array($id, $string))
			->object($file->write($string = (uniqid() . "\n")))->isIdenticalTo($file)
			->adapter($adapter)->call('fwrite', array($id, $string))
		;
	}
}

?>
