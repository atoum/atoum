<?php

namespace mageekguy\atoum\tests\units\reflection;

require __DIR__ . '/../../runner.php';

use
	atoum
;

class classWithoutConstructor {}

class classWithProtectedConstructor
{
	protected function __construct() {}
}

class classWithPrivateConstructor
{
	private function __construct() {}
}

class classWithFinalConstructor
{
	public final function __construct() {}
}

interface isAnInterface {}

abstract class abstractClass {}

class classWithConstructor
{
	public $a = null;
	public $b = null;
	public $c = null;
	public $reference = null;
	public $array = null;

	public function __construct($a, $b, $c, & $reference, $array = array())
	{
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
		$this->reference = $reference = uniqid();
		$this->array = $array;
	}
}

class factory extends atoum
{
	public function testBuild()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithoutConstructor')))->isInstanceOf('\closure')
				->object($factory())->isInstanceOf(__NAMESPACE__ . '\classWithoutConstructor')

				->object($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithoutConstructor'), $instance))->isInstanceOf('\closure')
				->object($builtInstance = $factory())->isInstanceOf(__NAMESPACE__ . '\classWithoutConstructor')
				->object($instance)->isIdenticalTo($builtInstance)

				->variable($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithProtectedConstructor')))->isNull

				->variable($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithProtectedConstructor'), $instance))->isNull

				->variable($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithPrivateConstructor')))->isNull

				->variable($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithPrivateConstructor'), $instance))->isNull

				->object($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithFinalConstructor')))->isInstanceOf('\closure')
				->object($factory())->isInstanceOf(__NAMESPACE__ . '\classWithFinalConstructor')

				->object($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithFinalConstructor'), $instance))->isInstanceOf('\closure')
				->object($builtInstance = $factory())->isInstanceOf(__NAMESPACE__ . '\classWithFinalConstructor')
				->object($instance)->isIdenticalTo($builtInstance)

				->variable($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\abstractClass')))->isNull

				->variable($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\abstractClass'), $instance))->isNull

				->variable($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\isAnInterface')))->isNull

				->variable($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\isAnInterface'), $instance))->isNull

				->object($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructor')))->isInstanceOf('\closure')
				->object($builtInstance = $factory('a', 'b', 'c', $reference))->isInstanceOf(__NAMESPACE__ . '\classWithConstructor')
				->string($builtInstance->a)->isEqualTo('a')
				->string($builtInstance->b)->isEqualTo('b')
				->string($builtInstance->c)->isEqualTo('c')
				->string($builtInstance->reference)->isNotEmpty
				->string($builtInstance->reference)->isEqualTo($reference)
				->array($builtInstance->array)->isEmpty

				->object($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructor')))->isInstanceOf('\closure')
				->object($builtInstance = $factory('a', 'b', 'c', $reference, $array = range(1, 5)))->isInstanceOf(__NAMESPACE__ . '\classWithConstructor')
				->string($builtInstance->a)->isEqualTo('a')
				->string($builtInstance->b)->isEqualTo('b')
				->string($builtInstance->c)->isEqualTo('c')
				->string($builtInstance->reference)->isNotEmpty
				->string($builtInstance->reference)->isEqualTo($reference)
				->array($builtInstance->array)->isEqualTo($array)

				->object($factory = $this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructor'), $instance))->isInstanceOf('\closure')
				->object($builtInstance = $factory('a', 'b', 'c', $reference))->isInstanceOf(__NAMESPACE__ . '\classWithConstructor')
				->object($instance)->isIdenticalTo($builtInstance)
				->string($instance->a)->isEqualTo('a')
				->string($instance->b)->isEqualTo('b')
				->string($instance->c)->isEqualTo('c')
				->string($instance->reference)->isNotEmpty
				->string($instance->reference)->isEqualTo($reference)
				->array($instance->array)->isEmpty

				->object($builtInstance = $factory('a', 'b', 'c', $reference, $array = range(1, 5)))->isInstanceOf(__NAMESPACE__ . '\classWithConstructor')
				->object($instance)->isIdenticalTo($builtInstance)
				->string($instance->a)->isEqualTo('a')
				->string($instance->b)->isEqualTo('b')
				->string($instance->c)->isEqualTo('c')
				->string($instance->reference)->isNotEmpty
				->string($instance->reference)->isEqualTo($reference)
				->array($instance->array)->isEqualTo($array)
		;
	}
}
