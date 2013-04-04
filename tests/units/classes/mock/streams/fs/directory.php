<?php

namespace mageekguy\atoum\tests\units\mock\streams\fs;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream,
	mageekguy\atoum\mock\streams\fs\directory as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class directory extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\mock\stream');
	}

	public function testMkdir()
	{
		$this
			->if($directory = testedClass::get())
			->and($directory->notExists())
			->then
				->boolean(mkdir($directory, 0777))->isTrue()
				->integer($directory->getPermissions())->isEqualTo(0777)
				->boolean(mkdir($directory, 0777))->isFalse()
			->if($directory->notExists())
			->then
				->boolean(mkdir($directory, 0007))->isTrue()
				->integer($directory->getPermissions())->isEqualTo(0007)
		;
	}

	public function testRmdir()
	{
		$this
			->if($directory = testedClass::get())
			->and($directory->notExists())
			->then
				->boolean(rmdir($directory))->isFalse()
			->if($directory->exists())
			->then
				->boolean(rmdir($directory))->isTrue()
			->if($directory->exists())
			->and($directory->isNotWritable())
			->then
				->boolean(rmdir($directory))->isFalse()
		;
	}
}
