<?php

namespace mageekguy\atoum\tests\units\factory\builder;

require __DIR__ . '/../../../runner.php';

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

class classWithConstructorWithOptionalArguments
{
	public function __construct($a = null, $b = null) {}
}

class closure extends atoum
{
	public function testClass()
	{
		$this->testedClass->implements('atoum\factory\builder');
	}

	public function testBuild()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithoutConstructor')))->isTestedInstance
				->object($factory = $this->testedInstance->get())->isInstanceOf('\closure')
				->object($factory())->isInstanceOf(__NAMESPACE__ . '\classWithoutConstructor')

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithoutConstructor'), $instance))->isTestedInstance
				->object($factory = $this->testedInstance->get())->isInstanceOf('\closure')
				->object($builtInstance = $factory())->isInstanceOf(__NAMESPACE__ . '\classWithoutConstructor')
				->object($instance)->isIdenticalTo($builtInstance)

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithProtectedConstructor')))->isTestedInstance
				->variable($this->testedInstance->get())->isNull

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithProtectedConstructor'), $instance))->isTestedInstance
				->variable($this->testedInstance->get())->isNull

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithPrivateConstructor')))->isTestedInstance
				->variable($this->testedInstance->get())->isNull

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithPrivateConstructor'), $instance))->isTestedInstance
				->variable($this->testedInstance->get())->isNull

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithFinalConstructor')))->isTestedInstance
				->object($factory = $this->testedInstance->get())->isInstanceOf('\closure')
				->object($factory())->isInstanceOf(__NAMESPACE__ . '\classWithFinalConstructor')

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithFinalConstructor'), $instance))->isTestedInstance
				->object($factory = $this->testedInstance->get())->isInstanceOf('\closure')
				->object($builtInstance = $factory())->isInstanceOf(__NAMESPACE__ . '\classWithFinalConstructor')
				->object($instance)->isIdenticalTo($builtInstance)

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\abstractClass')))->isTestedInstance
				->variable($this->testedInstance->get())->isNull

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\abstractClass'), $instance))->isTestedInstance
				->variable($this->testedInstance->get())->isNull

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\isAnInterface')))->isTestedInstance
				->variable($this->testedInstance->get())->isNull

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\isAnInterface'), $instance))->isTestedInstance
				->variable($this->testedInstance->get())->isNull

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructor')))->isTestedInstance
				->object($factory = $this->testedInstance->get())->isInstanceOf('\closure')
				->object($builtInstance = $factory('a', 'b', 'c', $reference))->isInstanceOf(__NAMESPACE__ . '\classWithConstructor')
				->string($builtInstance->a)->isEqualTo('a')
				->string($builtInstance->b)->isEqualTo('b')
				->string($builtInstance->c)->isEqualTo('c')
				->string($builtInstance->reference)->isNotEmpty
				->string($builtInstance->reference)->isEqualTo($reference)
				->array($builtInstance->array)->isEmpty

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructor')))->isTestedInstance
				->object($factory = $this->testedInstance->get())->isInstanceOf('\closure')
				->object($builtInstance = $factory('a', 'b', 'c', $reference, $array = range(1, 5)))->isInstanceOf(__NAMESPACE__ . '\classWithConstructor')
				->string($builtInstance->a)->isEqualTo('a')
				->string($builtInstance->b)->isEqualTo('b')
				->string($builtInstance->c)->isEqualTo('c')
				->string($builtInstance->reference)->isNotEmpty
				->string($builtInstance->reference)->isEqualTo($reference)
				->array($builtInstance->array)->isEqualTo($array)

				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructor'), $instance))->isTestedInstance
				->object($factory = $this->testedInstance->get())->isInstanceOf('\closure')
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

	/**
	 * @php >= 5.6
	 */
	public function testBuildWithVariadicArguments()
	{
		eval('namespace ' . __NAMESPACE__ . ' { class classWithConstructorWithVariadicArgument
		{
			public $variadicArguments;

			public function __construct(...$a)
			{
				$this->variadicArguments = $a;
			}
		} }');

		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructorWithVariadicArgument'), $instance))->isTestedInstance
				->object($factory = $this->testedInstance->get())->isInstanceOf('\closure')
				->object($builtInstance = $factory('a', 'b', 'c'))->isInstanceOf(__NAMESPACE__ . '\classWithConstructorWithVariadicArgument')
				->array($builtInstance->variadicArguments)->isEqualTo(array('a', 'b', 'c'))
		;
	}

	public function testAddToAssertionManager()
	{
		$this
			->given(
				$this->newTestedInstance,
				$assertionManager = new \mock\atoum\test\assertion\manager()
			)
			->then
				->object($this->testedInstance->addToAssertionManager($assertionManager, $factoryName = uniqid(), $defaultHandler = function() {}))->isTestedInstance
				->mock($assertionManager)
					->call('setMethodHandler')->withIdenticalArguments($factoryName, $defaultHandler)->once
					->call('setPropertyHandler')->withIdenticalArguments($factoryName, $defaultHandler)->once

			->if($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructor')))
			->then
				->object($this->testedInstance->addToAssertionManager($assertionManager, $factoryName = uniqid(), $defaultHandler = function() {}))->isTestedInstance
				->mock($assertionManager)
					->call('setMethodHandler')->withArguments($factoryName, $this->testedInstance->get())->once
					->call('setPropertyHandler')->withArguments($factoryName, $this->testedInstance->get())->never
					->call('setPropertyHandler')->withArguments($factoryName, $defaultHandler)->once

			->if($this->testedInstance->build(new \reflectionClass(__NAMESPACE__ . '\classWithConstructorWithOptionalArguments')))
			->then
				->object($this->testedInstance->addToAssertionManager($assertionManager, $factoryName = uniqid(), function() {}))->isTestedInstance
				->mock($assertionManager)
					->call('setMethodHandler')->withArguments($factoryName, $this->testedInstance->get())->once
					->call('setPropertyHandler')->withArguments($factoryName, $this->testedInstance->get())->once
		;
	}
}
