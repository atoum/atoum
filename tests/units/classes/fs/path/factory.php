<?php

namespace mageekguy\atoum\tests\units\fs\path;

use
	mageekguy\atoum,
	mageekguy\atoum\fs\path,
	mageekguy\atoum\fs\path\factory as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class factory extends atoum\test
{
	public function testSetDirectorySeparator()
	{
		$this
			->given($factory = new testedClass())
			->then
				->object($factory->setDirectorySeparator(DIRECTORY_SEPARATOR))->isIdenticalTo($factory)
				->object($factory->setDirectorySeparator('/'))->isIdenticalTo($factory)
				->object($factory->setDirectorySeparator('\\'))->isIdenticalTo($factory)
				->object($factory->setDirectorySeparator())->isIdenticalTo($factory)
		;
	}

	public function testBuild()
	{
		$this
			->given($factory = new testedClass())
			->then
				->object($factory->build($path = uniqid()))->isEqualTo(new path($path))
			->if($factory->setDirectorySeparator(DIRECTORY_SEPARATOR))
			->then
				->object($factory->build($path = uniqid()))->isEqualTo(new path($path, DIRECTORY_SEPARATOR))
			->if($factory->setDirectorySeparator('/'))
			->then
				->object($factory->build($path = uniqid()))->isEqualTo(new path($path, '/'))
			->if($factory->setDirectorySeparator('\\'))
			->then
				->object($factory->build($path = uniqid()))->isEqualTo(new path($path, '\\'))
			->if($factory->setDirectorySeparator())
			->then
				->object($factory->build($path = uniqid()))->isEqualTo(new path($path, DIRECTORY_SEPARATOR))
			->if($factory->setAdapter($adapter = new atoum\adapter()))
			->then
				->object($factory->build($path = uniqid()))->isEqualTo(new path($path, DIRECTORY_SEPARATOR, $adapter))
		;
	}
}
