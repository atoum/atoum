<?php

namespace mageekguy\atoum\tests\units\script\arguments;

use
	mageekguy\atoum,
	mageekguy\atoum\script
;

require_once __DIR__ . '/../../../runner.php';

class parser extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->hasInterface('iteratorAggregate');
	}

	public function test__construct()
	{
		$this->assert
			->if($parser = new script\arguments\parser())
			->then
				->object($parser->getSuperGlobals())->isEqualTo(new atoum\superglobals())
				->array($parser->getValues())->isEmpty()
				->array($parser->getHandlers())->isEmpty()
				->array($parser->getPriorities())->isEmpty()
				->object($parser->getIterator())->isEmpty()
			->if($parser = new script\arguments\parser($superglobals = new atoum\superglobals()))
			->then
				->object($parser->getSuperGlobals())->isIdenticalTo($superglobals)
				->array($parser->getValues())->isEmpty()
				->array($parser->getHandlers())->isEmpty()
				->array($parser->getPriorities())->isEmpty()
				->object($parser->getIterator())->isEmpty()
		;
	}

	public function testSetSuperglobals()
	{
		$this->assert
			->if($parser = new script\arguments\parser())
			->then
				->object($parser->setSuperglobals($superglobals = new atoum\superglobals()))->isIdenticalTo($parser)
				->object($parser->getSuperGlobals())->isIdenticalTo($superglobals)
		;
	}

	public function testGetValues()
	{
		$this
			->assert
				->if($script = new \mock\mageekguy\atoum\script(uniqid()))
				->and($parser = new script\arguments\parser())
				->then
					->array($parser->getValues())->isEmpty()
					->variable($parser->getValues(uniqid()))->isNull()
				->if($parser->addHandler(function($script, $argument, $value) {}, array('-a'))->parse($script, array('-a')))
				->then
					->array($parser->getValues())->isEqualTo(array('-a' => array()))
					->array($parser->getValues('-a'))->isEmpty()
					->variable($parser->getValues(uniqid()))->isNull()
				->if($parser->parse($script, array('-a', 'a1', 'a2')))
				->then
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
					->array($parser->getValues('-a'))->isEqualTo(array('a1', 'a2'))
					->variable($parser->getValues(uniqid()))->isNull()
				->if($parser->addHandler(function($script, $argument, $value) {}, array('-b'))->parse($script, array('-a', 'a1', 'a2', '-b')))
				->then
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
					->array($parser->getValues('-a'))->isEqualTo(array('a1', 'a2'))
					->array($parser->getValues('-b'))->isEmpty()
					->variable($parser->getValues(uniqid()))->isNull()
		;
	}

	public function testGetIterator()
	{
		$this
			->assert
				->if($script = new \mock\mageekguy\atoum\script(uniqid()))
				->and($parser = new script\arguments\parser())
				->and($parser->parse($script, array()))
				->then
					->object($parser->getIterator())
						->isInstanceOf('arrayIterator')
						->isEmpty()
				->if($parser
						->addHandler(function($script, $argument, $value) {}, array('-a'))
						->addHandler(function($script, $argument, $value) {}, array('-b'))
						->parse($script, array('-a', 'a1', 'a2', '-b'))
					)
				->then
					->object($parser->getIterator())
						->isInstanceOf('arrayIterator')
						->isEqualTo(new \arrayIterator($parser->getValues()))
		;
	}

	public function testParse()
	{
		$this
			->assert('when using $_SERVER')
				->if($script = new \mock\mageekguy\atoum\script(uniqid()))
				->and($superglobals = new atoum\superglobals())
				->and($superglobals->_SERVER['argv'] = array())
				->and($parser = new script\arguments\parser($superglobals))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEmpty()
				->if($superglobals->_SERVER['argv'] = array('scriptName'))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEmpty()
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-a'))
				->and($parser->addHandler(function($script, $argument, $value) use (& $invokeA) { $invokeA++; }, array('-a')))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array()))
					->integer($invokeA)->isEqualTo(1)
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-a', '-b'))
				->and($parser->addHandler(function($script, $argument, $value) use (& $invokeB) { $invokeB++; }, array('-b')))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array(), '-b' => array()))
					->integer($invokeA)->isEqualTo(2)
					->integer($invokeB)->isEqualTo(1)
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1'))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1')))
					->integer($invokeA)->isEqualTo(3)
					->integer($invokeB)->isEqualTo(1)
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2'))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
					->integer($invokeA)->isEqualTo(4)
					->integer($invokeB)->isEqualTo(1)
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b'))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
					->integer($invokeA)->isEqualTo(5)
					->integer($invokeB)->isEqualTo(2)
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3'))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3')))
					->integer($invokeA)->isEqualTo(6)
					->integer($invokeB)->isEqualTo(3)
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c'))
				->and($parser->addHandler(function($script, $argument, $value) use (& $invokeC) { $invokeC++; }, array('--c')))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3'), '--c' => array()))
					->integer($invokeA)->isEqualTo(7)
					->integer($invokeB)->isEqualTo(4)
					->integer($invokeC)->isEqualTo(1)
			->assert('when using argument')
				->if($superglobals->_SERVER['argv'] = array())
				->then
					->object($parser->parse($script, array()))->isIdenticalTo($parser)
					->array($parser->getValues())->isEmpty()
					->integer($invokeA)->isEqualTo(7)
					->integer($invokeB)->isEqualTo(4)
					->integer($invokeC)->isEqualTo(1)
					->object($parser->parse($script, array('-a')))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array()))
					->integer($invokeA)->isEqualTo(8)
					->integer($invokeB)->isEqualTo(4)
					->integer($invokeC)->isEqualTo(1)
					->object($parser->parse($script, array('-a', '-b')))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array(), '-b' => array()))
					->integer($invokeA)->isEqualTo(9)
					->integer($invokeB)->isEqualTo(5)
					->integer($invokeC)->isEqualTo(1)
					->object($parser->parse($script, array('-a', 'a1')))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1')))
					->integer($invokeA)->isEqualTo(10)
					->integer($invokeB)->isEqualTo(5)
					->integer($invokeC)->isEqualTo(1)
					->object($parser->parse($script, array('-a', 'a1', 'a2')))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
					->integer($invokeA)->isEqualTo(11)
					->integer($invokeB)->isEqualTo(5)
					->integer($invokeC)->isEqualTo(1)
					->object($parser->parse($script, array('-a', 'a1', 'a2', '-b')))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
					->integer($invokeA)->isEqualTo(12)
					->integer($invokeB)->isEqualTo(6)
					->integer($invokeC)->isEqualTo(1)
					->object($parser->parse($script, array('-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3')))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3')))
					->integer($invokeA)->isEqualTo(13)
					->integer($invokeB)->isEqualTo(7)
					->integer($invokeC)->isEqualTo(1)
					->object($parser->parse($script, array('-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c')))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3'), '--c' => array()))
					->integer($invokeA)->isEqualTo(14)
					->integer($invokeB)->isEqualTo(8)
					->integer($invokeC)->isEqualTo(2)
					->exception(function() use ($parser, $script) {
							$parser->parse($script, array('b'));
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime\unexpectedValue')
						->hasMessage('First argument \'b\' is invalid')
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '-d', 'd1', 'd2', '--c'))
				->and($parser->addHandler(function($script, $argument, $value) {}, array('-d'), PHP_INT_MAX))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-d' => array('d1', 'd2'), '-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3'), '--c' => array()))
				->if($superglobals->_SERVER['argv'] = array('scriptName', '-d', 'd1', 'd2', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c'))
				->then
					->object($parser->parse($script))->isIdenticalTo($parser)
					->array($parser->getValues())->isEqualTo(array('-d' => array('d1', 'd2'), '-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3'), '--c' => array()))
		;
	}

	public function testIsArgument()
	{
		$this->assert
			->boolean(script\arguments\parser::isArgument(uniqid()))->isFalse()
			->boolean(script\arguments\parser::isArgument('+' . rand(0, 9)))->isFalse()
			->boolean(script\arguments\parser::isArgument('-' . rand(0, 9)))->isFalse()
			->boolean(script\arguments\parser::isArgument('--' . rand(0, 9)))->isFalse()
			->boolean(script\arguments\parser::isArgument('+_'))->isFalse()
			->boolean(script\arguments\parser::isArgument('-_'))->isFalse()
			->boolean(script\arguments\parser::isArgument('--_'))->isFalse()
			->boolean(script\arguments\parser::isArgument('+-'))->isFalse()
			->boolean(script\arguments\parser::isArgument('---'))->isFalse()
			->boolean(script\arguments\parser::isArgument('+a'))->isTrue()
			->boolean(script\arguments\parser::isArgument('-a'))->isTrue()
			->boolean(script\arguments\parser::isArgument('--a'))->isTrue()
			->boolean(script\arguments\parser::isArgument('+a' . rand(0, 9)))->isTrue()
			->boolean(script\arguments\parser::isArgument('-a' . rand(0, 9)))->isTrue()
			->boolean(script\arguments\parser::isArgument('--a' . rand(0, 9)))->isTrue()
			->boolean(script\arguments\parser::isArgument('+aa'))->isTrue()
			->boolean(script\arguments\parser::isArgument('-aa'))->isTrue()
			->boolean(script\arguments\parser::isArgument('--aa'))->isTrue()
		;
	}

	public function testAddHandler()
	{
		$this->assert
			->if($parser = new script\arguments\parser())
			->then
				->object($parser->addHandler($handler = function($script, $argument, $values) {}, $arguments = array($argument = '-a')))->isIdenticalTo($parser)
				->array($parser->getHandlers())->isEqualTo(array($argument => array($handler)))
				->array($parser->getPriorities())->isEqualTo(array($argument => 0))
				->object($parser->addHandler($handler, $arguments))->isIdenticalTo($parser)
				->array($parser->getHandlers())->isEqualTo(array($argument => array($handler, $handler)))
				->array($parser->getPriorities())->isEqualTo(array($argument => 0))
				->exception(function() use ($parser) {
							$parser->addHandler(function() {}, $argument = array('-b'));
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Handler must take three arguments')
				->exception(function() use ($parser) {
							$parser->addHandler(function($script) {}, array('-b'));
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Handler must take three arguments')
				->exception(function() use ($parser) {
							$parser->addHandler(function($script, $argument) {}, array('-b'));
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Handler must take three arguments')
				->exception(function() use ($parser, & $badArgument) {
							$parser->addHandler(function($script, $argument, $values) {}, array($badArgument = 'b'));
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Argument \'' . $badArgument . '\' is invalid')
				->object($parser->addHandler($otherHandler = function($script, $argument, $values) {}, array($otherArgument = '-b'), $priority = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($parser)
				->array($parser->getHandlers())->isEqualTo(array($argument => array($handler, $handler), $otherArgument => array($otherHandler)))
				->array($parser->getPriorities())->isEqualTo(array($argument => 0, $otherArgument => $priority))
		;
	}

	public function testResetHandlers()
	{
		$this->assert
			->if($parser = new script\arguments\parser())
			->then
				->object($parser->resetHandlers())->isIdenticalTo($parser)
				->array($parser->getHandlers())->isEmpty()
				->array($parser->getPriorities())->isEmpty()
			->if($parser->addHandler(function($script, $argument, $values) {}, array('-a')))
			->then
				->object($parser->resetHandlers())->isIdenticalTo($parser)
				->array($parser->getHandlers())->isEmpty()
				->array($parser->getPriorities())->isEmpty()
		;
	}
}
