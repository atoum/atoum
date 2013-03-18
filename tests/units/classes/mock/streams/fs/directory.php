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
				->integer((int) sprintf('%o', fileperms($directory)))->isEqualTo(0777)
				->boolean(mkdir($directory, 0777))->isFalse()
			->if($directory->notExists())
			->then
				->boolean(mkdir($directory, 0007))->isTrue()
				->integer((int) sprintf('%o', fileperms($directory)))->isEqualTo(0007)
		;
	}
}
