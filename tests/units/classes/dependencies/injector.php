<?php

namespace mageekguy\atoum\tests\units\dependencies;

use
	mageekguy\atoum\test,
	mageekguy\atoum\dependencies\injector as testedClass
;

require_once __DIR__ . '/../../runner.php';

class injector extends test
{
	public function testClass()
	{
		$this->testedClass->hasInterface('arrayAccess');
	}

	public function test__construct()
	{
		$this
			->if($injector = new testedClass($closure = function() {}))
			->then
				->object($injector->getClosure())->isIdenticalTo($closure)
				->array($injector->getArguments())->isEmpty()
		;
	}

	public function test__invoke()
	{
		$this
			->if($injector = new testedClass(function() use (& $return) { return ($return = uniqid()); }))
			->then
				->string($injector())->isEqualTo($return)
			->if($injector = new testedClass(function($argument) { return $argument; }))
			->and($injector->setArgument(1, $argument = uniqid()))
			->then
				->string($injector())->isEqualTo($argument)
			->if($injector = new testedClass(function($argument1, $argument2) { return $argument1 . $argument2; }))
			->and($injector->setArgument(1, $argument1 = uniqid()))
			->and($injector->setArgument(2, $argument2 = uniqid()))
			->then
				->string($injector())->isEqualTo($argument1 . $argument2)
			->if($injector = new testedClass(function($a, $b) { return $a . $b; }))
			->and($injector->setArgument('b', $valueB = uniqid()))
			->and($injector->setArgument('a', $valueA = uniqid()))
			->then
				->string($injector())->isEqualTo($valueB . $valueA)
				->string($injector($otherValueA = uniqid(), $otherValueB = uniqid()))->isEqualTo($otherValueA . $otherValueB)
		;
	}

	public function test__get()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { $injector->{$argument}; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
			->then
				->string($injector->{$argument})->isEqualTo($value)
		;
	}

	public function test__set()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->and($injector->a = $argument1 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array('a' => $argument1))
			->if($injector->b = $argument2 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array('a' => $argument1, 'b' => $argument2))
			->if($injector = new testedClass(function() {}))
			->and($injector->b = $argument2 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array('b' => $argument2))
			->if($injector->a = $argument1 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array('b' => $argument2, 'a' => $argument1))
		;
	}

	public function test__isset()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->boolean(isset($injector->{uniqid()}))->isFalse()
			->if($injector->setArgument($argument = uniqid(), uniqid()))
			->then
				->boolean(isset($injector->{$argument}))->isTrue()
		;
	}

	public function test__unset()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { unset($injector->{$argument = uniqid()}); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
			->when(function() use ($injector, $argument) { unset($injector->{$argument}); })
			->then
				->boolean(isset($injector[$argument]))->isFalse()
		;
	}

	public function testSetArgument()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument(1, $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isIdenticalTo(array(1 => $argument1))
				->object($injector->setArgument(2, $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isIdenticalTo(array(1 => $argument1, 2 => $argument2))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument(2, $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isIdenticalTo(array(2 => $argument2))
				->object($injector->setArgument(1, $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isIdenticalTo(array(2 => $argument2, 1 => $argument1))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument('a', $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isIdenticalTo(array('a' => $argument1))
				->object($injector->setArgument('b', $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isIdenticalTo(array('a' => $argument1, 'b' => $argument2))
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument('b', $argument2 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isIdenticalTo(array('b' => $argument2))
				->object($injector->setArgument('a', $argument1 = uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isIdenticalTo(array('b' => $argument2, 'a' => $argument1))
		;
	}

	public function testGetArgument()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { $injector->getArgument($argument = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
			->then
				->string($injector->getArgument($argument))->isEqualTo($value)
		;
	}

	public function testArgumentExists()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->boolean($injector->argumentExists(uniqid()))->isFalse()
			->if($injector->setArgument($argument = uniqid(), uniqid()))
			->then
				->boolean($injector->argumentExists($argument))->isTrue()
		;
	}

	public function testUnsetArgument()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { $injector->unsetArgument($argument = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
			->then
				->object($injector->unsetArgument($argument))->isIdenticalTo($injector)
				->boolean($injector->argumentExists($argument))->isFalse()
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->and($injector[1] = $argument1 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array(1 => $argument1))
			->if($injector[2] = $argument2 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array(1 => $argument1, 2 => $argument2))
			->if($injector = new testedClass(function() {}))
			->if($injector[2] = $argument2 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array(2 => $argument2))
			->if($injector[1] = $argument1 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array(2 => $argument2, 1 => $argument1))
			->if($injector = new testedClass(function() {}))
			->and($injector['a'] = $argument1 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array('a' => $argument1))
			->if($injector['b'] = $argument2 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array('a' => $argument1, 'b' => $argument2))
			->if($injector = new testedClass(function() {}))
			->and($injector['b'] = $argument2 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array('b' => $argument2))
			->if($injector['a'] = $argument1 = uniqid())
			->then
				->array($injector->getArguments())->isIdenticalTo(array('b' => $argument2, 'a' => $argument1))
		;
	}

	public function testOffsetGet()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { $injector[$argument = uniqid()]; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
			->then
				->string($injector[$argument])->isEqualTo($value)
		;
	}

	public function testOffsetExists()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->boolean(isset($injector[uniqid()]))->isFalse()
			->if($injector->setArgument($argument = uniqid(), uniqid()))
			->then
				->boolean(isset($injector[$argument]))->isTrue()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { unset($injector[$argument = uniqid()]); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
			->when(function() use ($injector, $argument) { unset($injector[$argument]); })
			->then
				->boolean(isset($injector[$argument]))->isFalse()
		;
	}
}

?>
