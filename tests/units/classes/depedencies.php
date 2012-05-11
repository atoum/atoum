<?php

namespace mageekguy\atoum\tests\units;

require_once __DIR__ . '/../runner.php';

use
	mageekguy\atoum
;

class depedencies extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->hasInterface('arrayAccess');
	}

	public function test__construct()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->then
				->array($depedencies->getInjectors())->isEmpty()
		;
	}

	public function test__invoke()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->then
				->object($depedencies())->isIdenticalTo($depedencies)
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($depedencies = new atoum\depedencies())
			->then
				->object($depedencies->offsetSet($key = uniqid(), $injector = function() {}))->isIdenticalTo($depedencies)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
				->object($depedencies->offsetSet($otherKey = uniqid(), $injectorValue = uniqid()))->isIdenticalTo($depedencies)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
					->boolean(isset($depedencies[$otherKey]))->isTrue()
					->object($depedencies[$otherKey])->isInstanceOf('closure')
					->string($depedencies[$otherKey]())->isEqualTo($injectorValue)
				->object($depedencies->offsetSet($this, $otherInjectorValue = uniqid()))->isIdenticalTo($depedencies)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
					->boolean(isset($depedencies[$otherKey]))->isTrue()
					->object($depedencies[$otherKey])->isInstanceOf('closure')
					->string($depedencies[$otherKey]())->isEqualTo($injectorValue)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
					->boolean(isset($depedencies[$otherKey]))->isTrue()
					->object($depedencies[$otherKey])->isInstanceOf('closure')
					->string($depedencies[$otherKey]())->isEqualTo($injectorValue)
					->boolean(isset($depedencies[$this]))->isTrue()
					->object($depedencies[$this])->isInstanceOf('closure')
					->string($depedencies[$this]())->isEqualTo($otherInjectorValue)
				->object($depedencies->offsetSet($otherKey, $otherDepedencies = new atoum\depedencies()))->isIdenticalTo($depedencies)
					->boolean(isset($depedencies[$key]))->isTrue()
					->object($depedencies[$key])->isIdenticalTo($injector)
					->boolean(isset($depedencies[$otherKey]))->isTrue()
					->object($depedencies[$otherKey])->isIdenticalTo($otherDepedencies)
		;
	}
}

?>
