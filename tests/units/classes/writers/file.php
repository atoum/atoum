<?php

namespace mageekguy\atoum\tests\units\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\writers
;

require_once(__DIR__ . '/../../runner.php');

class file extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->hasInterface('mageekguy\atoum\adapter\aggregator')
				->hasInterface('mageekguy\atoum\report\writers\realtime')
				->hasInterface('mageekguy\atoum\report\writers\asynchronous')
		;
	}

	public function test__construct()
	{
		$file = new writers\file();

		$this->assert
			->object($file->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->string($file->getFilename())->isEqualTo('atoum.log')
		;

		$adapter = new atoum\test\adapter();
		$adapter->fopen = function() {};
		$adapter->fclose = function() {};

		$file = new writers\file(null, $adapter);

		$this->assert
			->object($file->getAdapter())->isIdenticalTo($adapter)
			->string($file->getFilename())->isEqualTo('atoum.log')
		;

		$file = new writers\file('test.log');

		$this->assert
			->string($file->getFilename())->isEqualTo('test.log')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(writers\file::defaultFileName)->isEqualTo('atoum.log')
		;
	}

	public function test__destruct()
	{
		$id = uniqid();

		$adapter = new atoum\test\adapter();
		$adapter->fopen = function() use ($id) { return $id; };
		$adapter->fwrite = function() {};
		$adapter->fclose = function() {};
		$adapter->is_writable = function() { return true; };
		$adapter->is_null = function() { return true; };

		$file = new writers\file(null, $adapter);

		$file->write('something');

		$adapter->is_null = function() { return false; };

		unset($file);

		$this->assert
			->adapter($adapter)->call('is_null', array($id))
			->adapter($adapter)->call('fclose', array($id))
			;

	}

	public function testWrite()
	{
		$id = uniqid();

		$adapter = new atoum\test\adapter();
		$adapter->fopen = function() use ($id) { return $id; };
		$adapter->fclose = function() {};
		$adapter->fwrite = function() {};
		$adapter->is_writable = function() { return true; };
		$adapter->is_null = function() { return true; };

		$file = new writers\file(null,$adapter);
		$obj = $file->write($string = uniqid());

		$this->assert
			->object($obj)->isIdenticalTo($file)
			->adapter($adapter)->call('is_null', array(null))
			->adapter($adapter)->call('dirname', array('atoum.log'))
			->adapter($adapter)->call('is_writable', array('.'))
			->adapter($adapter)->call('fopen', array('atoum.log', 'w'))
			->adapter($adapter)->call('fwrite', array($id, $string))
			->object($file->write($string = (uniqid() . "\n")))->isIdenticalTo($file)
			->adapter($adapter)->call('fwrite', array($id, $string))
		;

		$adapter->is_null = function() { return false; };

		$this->assert
			->object($file->write($string = uniqid()))->isIdenticalTo($file)
			->adapter($adapter)->call('is_null', array($id))
			->adapter($adapter)->call('fwrite', array($id, $string))
		;
	}

	public function testSetFilename()
	{
		$id = uniqid();

		$adapter = new atoum\test\adapter();
		$adapter->fopen = function() use ($id) { return $id; };
		$adapter->fclose = function() {};
		$adapter->fwrite = function() {};
		$adapter->is_writable = function() { return true; };
		$adapter->is_null = function() { return true; };

		$file = new writers\file(null,$adapter);

		$this->assert
			->string($file->getFilename())->isEqualTo('atoum.log')
			;

		$file->setFilename('anotherName');

		$this->assert
			->string($file->getFilename())->isEqualTo('anotherName')
			;

		$adapter->is_null = function() { return false; };

		$obj = $file->write($string = uniqid());

		$file->setFilename('anotherNameAgain');

		$this->assert
			->string($file->getFilename())->isEqualTo('anotherName')
			;
	}

	public function testGetFilename()
	{
		$file = new writers\file();

		$this->assert
			->string($file->getFilename())->isEqualTo('atoum.log')
			;

		$file->setFilename('anotherName');

		$this->assert
			->string($file->getFilename())->isEqualTo('anotherName')
			;
	}
}

?>
