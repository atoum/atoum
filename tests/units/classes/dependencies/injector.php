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
				->array($injector->getAvailableArguments())->isEmpty()
			->if($injector = new testedClass($closure = function($a, $b = 'b') {}))
			->then
				->object($injector->getClosure())->isIdenticalTo($closure)
				->array($injector->getArguments())->isEmpty()
				->array($injector->getAvailableArguments())->isEqualTo(array('a', 'b'))
		;
	}

	public function test__invoke()
	{
		$this
			->if($injector = new testedClass(function() use (& $return) { return ($return = uniqid()); }))
			->then
				->string($injector())->isEqualTo($return)
			->if($injector = new testedClass(function($a) { return $a; }))
			->and($injector->setArgument('a', $a = uniqid()))
			->then
				->string($injector())->isEqualTo($a)
			->if($injector = new testedClass(function($a1, $a2) { return $a1 . $a2; }))
			->and($injector->setArgument('a1', $a1 = uniqid()))
			->and($injector->setArgument('a2', $a2 = uniqid()))
			->then
				->string($injector())->isEqualTo($a1 . $a2)
			->if($injector = new testedClass(function($a1, $a2) { return $a1 . $a2; }))
			->and($injector->setArgument('a2', $a2 = uniqid()))
			->and($injector->setArgument('a1', $a1 = uniqid()))
			->then
				->string($injector())->isEqualTo($a1 . $a2)
			->if($injector = new testedClass(function($a1, $a2) { return $a1 . $a2; }))
			->and($injector->setArgument('a1', $a1 = uniqid()))
			->then
				->exception(function() use ($injector) { $injector(); })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'a2\' is missing')
			->if($injector = new testedClass(function($a1, $a2 = 'foo') { return $a1 . $a2; }))
			->and($injector->setArgument('a1', $a1 = uniqid()))
			->then
				->string($injector())->isEqualTo($a1 . 'foo')
			->if($injector->setArgument('a2', $a2 = uniqid()))
			->then
				->string($injector())->isEqualTo($a1 . $a2)
		;
	}

	public function test__get()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { $injector->{$argument = uniqid()}; })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector = new testedClass(function($a) {}))
			->then
				->exception(function() use ($injector) { $injector->a; })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'a\' is undefined')
			->if($injector->setArgument('a', $value = uniqid()))
			->then
				->string($injector->a)->isEqualTo($value)
		;
	}

	public function test__set()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->and($injector->{uniqid()} = uniqid())
			->then
				->array($injector->getArguments())->isEmpty()
			->if($injector = new testedClass(function($a) {}))
			->and($injector->a = $value = uniqid())
			->then
				->string($injector->getArgument('a'))->isEqualTo($value)
			->if($injector->b = uniqid())
			->then
				->boolean(isset($injector->b))->isFalse()
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
				->boolean(isset($injector->{$argument}))->isFalse()
			->if($injector = new testedClass(function($a) {}))
			->then
				->boolean(isset($injector->a))->isFalse()
			->if($injector->setArgument('a', uniqid()))
			->then
				->boolean(isset($injector->a))->isTrue()
		;
	}

	public function test__unset()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { unset($injector->{$argument = uniqid()}); })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
				->exception(function() use ($injector, & $argument) { unset($injector->{$argument = uniqid()}); })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector = new testedClass(function($a) {}))
			->and($injector->a = uniqid())
			->when(function() use ($injector) { unset($injector->a); })
			->then
				->boolean(isset($injector->a))->isFalse()
		;
	}

	public function testSetArgument()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->object($injector->setArgument(uniqid(), uniqid()))->isIdenticalTo($injector)
				->array($injector->getArguments())->isEmpty()
			->if($injector = new testedClass(function($a) {}))
			->and($injector->setArgument('a', $value = uniqid()))
			->then
				->string($injector->getArgument('a'))->isEqualTo($value)
			->if($injector->setArgument('b', uniqid()))
			->then
				->boolean(isset($injector->b))->isFalse()
		;
	}

	public function testGetArgument()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { $injector->getArgument($argument = uniqid()); })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector = new testedClass(function($a) {}))
			->and($injector->setArgument('a', $value = uniqid()))
			->then
				->string($injector->getArgument('a'))->isEqualTo($value)
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
				->boolean($injector->argumentExists($argument))->isFalse()
			->if($injector = new testedClass(function($a) {}))
			->and($injector->setArgument('a', uniqid()))
			->then
				->boolean($injector->argumentExists('a'))->isTrue()
		;
	}

	public function testUnsetArgument()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { $injector->unsetArgument($argument = uniqid()); })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
				->exception(function() use ($injector, & $argument) { $injector->unsetArgument($argument = uniqid()); })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector = new testedClass(function($a) {}))
			->and($injector->a = uniqid())
			->then
				->object($injector->unsetArgument('a'))->isIdenticalTo($injector)
				->boolean(isset($injector->a))->isFalse()
		;
	}

	public function testOffsetSet()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->and($injector[uniqid()] = uniqid())
			->then
				->array($injector->getArguments())->isEmpty()
			->if($injector = new testedClass(function($a) {}))
			->and($injector['a'] = $value = uniqid())
			->then
				->string($injector->getArgument('a'))->isEqualTo($value)
			->if($injector['b'] = uniqid())
			->then
				->boolean(isset($injector->b))->isFalse()
		;
	}

	public function testOffetGet()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { $injector[$argument = uniqid()]; })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector = new testedClass(function($a) {}))
			->then
				->exception(function() use ($injector) { $injector['a']; })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'a\' is undefined')
			->if($injector->setArgument('a', $value = uniqid()))
			->then
				->string($injector['a'])->isEqualTo($value)
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
				->boolean(isset($injector[$argument]))->isFalse()
			->if($injector = new testedClass(function($a) {}))
			->then
				->boolean(isset($injector['a']))->isFalse()
			->if($injector->setArgument('a', uniqid()))
			->then
				->boolean(isset($injector['a']))->isTrue()
		;
	}

	public function testOffsetUnset()
	{
		$this
			->if($injector = new testedClass(function() {}))
			->then
				->exception(function() use ($injector, & $argument) { unset($injector[$argument = uniqid()]); })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector->setArgument($argument = uniqid(), $value = uniqid()))
				->exception(function() use ($injector, & $argument) { unset($injector[$argument = uniqid()]); })
					->isInstanceOf('mageekguy\atoum\dependencies\injector\exception')
					->hasMessage('Argument \'' . $argument . '\' is undefined')
			->if($injector = new testedClass(function($a) {}))
			->and($injector->a = uniqid())
			->when(function() use ($injector) { unset($injector['a']); })
			->then
				->boolean(isset($injector->a))->isFalse()
		;
	}
}

?>
