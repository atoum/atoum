<?php

namespace mageekguy\atoum\tests\units\mock\generator;

require __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\mock\generator\method as testedClass
;

class method extends atoum
{
	public function test__construct()
	{
		$this
			->if($method = new testedClass())
			->then
				->variable($method->getName())->isNull()
				->object($method->getMockGenerator())->isInstanceOf('mageekguy\atoum\mock\generator')
			->if($method = new testedClass($mockGenerator = new atoum\mock\generator()))
			->then
				->variable($method->getName())->isNull()
				->object($method->getMockGenerator())->isIdenticalTo($mockGenerator)
			->if($method = new testedClass($mockGenerator = new atoum\mock\generator(), $name = uniqid()))
			->then
				->string($method->getName())->isEqualTo($name)
				->object($method->getMockGenerator())->isIdenticalTo($mockGenerator)
		;
	}

	public function testSetName()
	{
		$this
			->if($method = new testedClass())
			->then
				->object($method->setName($name = uniqid()))->isIdenticalTo($method)
				->string($method->getName())->isEqualTo($name)
				->object($method->setName($name = rand(1, PHP_INT_MAX)))->isIdenticalTo($method)
				->string($method->getName())->isEqualTo((string) $name)
			->if($method = new testedClass(null, uniqid()))
			->then
				->object($method->setName($name = uniqid()))->isIdenticalTo($method)
				->string($method->getName())->isEqualTo($name)
		;
	}

	public function test__get()
	{
		$this
			->if($method = new testedClass())
			->then
				->object($method->{$name = uniqid()})->isIdenticalTo($method)
				->string($method->getName())->isEqualTo($name)
				->object($method->{$otherName = uniqid()})->isIdenticalTo($method)
				->string($method->getName())->isEqualTo($otherName)
		;
	}

	public function test__set()
	{
		$this
			->if($method = new testedClass())
			->and($method->{$name = 'doesSomething'} = $returnValue = uniqid())
			->when(function() use (& $mockedInstance) { $mockedInstance = new \mock\object(); })
			->then
				->string($mockedInstance->doesSomething())->isEqualTo($returnValue)
		;
	}

	public function testSetMockGenerator()
	{
		$this
			->if($method = new testedClass())
			->then
				->object($method->setMockGenerator($mockGenerator = new atoum\mock\generator()))->isIdenticalTo($method)
				->object($method->getMockGenerator())->isIdenticalTo($mockGenerator)
				->object($method->setMockGenerator())->isIdenticalTo($method)
				->object($method->getMockGenerator())
					->isNotIdenticalTo($mockGenerator)
					->isInstanceOf('mageekguy\atoum\mock\generator')
		;
	}

	public function testCanHaveNoArgument()
	{
		$this
			->if($method = new testedClass())
			->and($mockGenerator = new \mock\mageekguy\atoum\mock\generator())
			->and($method->setMockGenerator($mockGenerator))
			->then
				->exception(function() use ($method) { $method->canHaveNoArgument(); })
					->isInstanceOf('mageekguy\atoum\mock\generator\method\exception')
					->hasMessage('Method name is undefined')
				->object($method->{$name = uniqid()}->canHaveNoArgument())->isIdenticalTo($method)
				->mock($mockGenerator)->call('orphanize')->withArguments($name)->once()
		;
	}

	public function testCanNotCallItsParent()
	{
		$this
			->if($method = new testedClass())
			->and($mockGenerator = new \mock\mageekguy\atoum\mock\generator())
			->and($method->setMockGenerator($mockGenerator))
			->then
				->exception(function() use ($method) { $method->canNotCallItsParent(); })
					->isInstanceOf('mageekguy\atoum\mock\generator\method\exception')
					->hasMessage('Method name is undefined')
				->object($method->{$name = uniqid()}->canNotCallItsParent())->isIdenticalTo($method)
				->mock($mockGenerator)->call('shunt')->withArguments($name)->once()
		;
	}

	public function testDoNothing()
	{
		$this
			->if($method = new testedClass())
			->then
				->exception(function() use ($method) { $method->doesNothing(); })
					->isInstanceOf('mageekguy\atoum\mock\generator\method\exception')
					->hasMessage('Method name is undefined')
			->if($method->{$name = 'doesSomething'} = $returnValue = uniqid())
			->then
				->object($method->{$name = 'doesSomething'}->doesNothing())->isIdenticalTo($method)
				->when(function() use (& $mockedInstance) { $mockedInstance = new \mock\object(); })
				->then
					->variable($mockedInstance->doesSomething())->isNull()
		;
	}
}
