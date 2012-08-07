<?php

namespace mageekguy\atoum\tests\units\fcgi\requests;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi\requests\post as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class post extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\fcgi\request')
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($request = new testedClass())
			->then
				->object($request->offsetSet($name = uniqid(), $value = uniqid()))->isIdenticalTo($request)
				->string($request->offsetGet($name))->isEqualTo($value)
				->string($request->getStdin())->isEqualTo(http_build_query(array($name => $value)))
				->object($request->offsetSet($name, $otherValue = uniqid()))->isIdenticalTo($request)
				->string($request->offsetGet($name))->isEqualTo($otherValue)
				->object($request->offsetSet($otherName = uniqid(), $value))->isIdenticalTo($request)
				->string($request->offsetGet($name))->isEqualTo($otherValue)
				->string($request->offsetGet($otherName))->isEqualTo($value)
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($request = new testedClass())
			->then
				->variable($request->offsetGet(uniqid()))->isNull()
			->if($request->offsetSet($name = uniqid(), $value = uniqid()))
			->then
				->string($request->offsetGet($name))->isEqualTo($value)
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($request = new testedClass())
			->then
				->boolean($request->offsetExists(uniqid()))->isFalse()
			->if($request->offsetSet($name = uniqid(), $value = uniqid()))
			->then
				->boolean($request->offsetExists($name))->isTrue()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($request = new testedClass())
			->then
				->object($request->offsetUnset(uniqid()))->isIdenticalTo($request)
			->if($request->offsetSet($name = uniqid(), $value = uniqid()))
			->then
				->object($request->offsetUnset($name))->isIdenticalTo($request)
				->boolean($request->offsetExists($name))->isFalse()
		;
	}
}
