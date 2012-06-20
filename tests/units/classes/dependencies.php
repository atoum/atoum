<?php

namespace mageekguy\atoum\tests\units;

require_once __DIR__ . '/../runner.php';

use
	mageekguy\atoum\test,
	mageekguy\atoum\dependencies as testedClass
;

class dependencies extends test
{
	public function testClass()
	{
		$this->testedClass->hasInterface('arrayAccess');
	}

	public function test__construct()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->array($dependencies->getInjectors())->isEmpty()
		;
	}

	public function testGet()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->variable($dependencies->get(uniqid()))->isNull()
			->if($dependencies[$key = uniqid()] = $injector = function() {})
			->then
				->variable($dependencies->get(uniqid()))->isNull()
				->object($dependencies->get($key))->isInstanceOf('mageekguy\atoum\dependencies\injector')
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies->offsetSet($key = uniqid(), $injector = function() {}))->isIdenticalTo($dependencies)
					->boolean(isset($dependencies[$key]))->isTrue()
					->object($dependencies[$key])->isInstanceOf('mageekguy\atoum\dependencies\injector')
				->object($dependencies->offsetSet($otherKey = uniqid(), $injectorValue = uniqid()))->isIdenticalTo($dependencies)
					->boolean(isset($dependencies[$key]))->isTrue()
					->object($dependencies[$key])->isInstanceOf('mageekguy\atoum\dependencies\injector')
					->boolean(isset($dependencies[$otherKey]))->isTrue()
					->object($dependencies[$otherKey])->isInstanceOf('mageekguy\atoum\dependencies\injector')
					->string($dependencies[$otherKey]())->isEqualTo($injectorValue)
				->object($dependencies->offsetSet($this, $otherInjectorValue = uniqid()))->isIdenticalTo($dependencies)
					->boolean(isset($dependencies[$key]))->isTrue()
					->object($dependencies[$key])->isInstanceOf('mageekguy\atoum\dependencies\injector')
					->boolean(isset($dependencies[$otherKey]))->isTrue()
					->object($dependencies[$otherKey])->isInstanceOf('mageekguy\atoum\dependencies\injector')
					->string($dependencies[$otherKey]())->isEqualTo($injectorValue)
					->boolean(isset($dependencies[$key]))->isTrue()
					->object($dependencies[$key])->isInstanceOf('mageekguy\atoum\dependencies\injector')
					->boolean(isset($dependencies[$otherKey]))->isTrue()
					->object($dependencies[$otherKey])->isInstanceOf('mageekguy\atoum\dependencies\injector')
					->string($dependencies[$otherKey]())->isEqualTo($injectorValue)
					->boolean(isset($dependencies[$this]))->isTrue()
					->object($dependencies[$this])->isInstanceOf('mageekguy\atoum\dependencies\injector')
					->string($dependencies[$this]())->isEqualTo($otherInjectorValue)
				->object($dependencies->offsetSet($otherKey, $otherDependencies = new testedClass()))->isIdenticalTo($dependencies)
					->boolean(isset($dependencies[$key]))->isTrue()
					->object($dependencies[$key])->isInstanceOf('mageekguy\atoum\dependencies\injector')
					->boolean(isset($dependencies[$otherKey]))->isTrue()
					->object($dependencies[$otherKey])->isIdenticalTo($otherDependencies)
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($dependencies = new testedClass())
			->then
				->object($dependencies[$class = uniqid()])->isInstanceOf($dependencies)
				->boolean(isset($dependencies[$class]))->isTrue()
			->if($dependencies['mageekguy\atoum\test'] = $testDependencies = new testedClass())
			->then
				->object($dependencies[$this])->isIdenticalTo($testDependencies)
		;
	}
}

?>
