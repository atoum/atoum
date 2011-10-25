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
		$this->assert
			->testedClass->hasInterface('iteratorAggregate')
		;
	}

	public function test__construct()
	{
		$parser = new script\arguments\parser();

		$this->assert
			->object($parser->getSuperGlobals())->isEqualTo(new atoum\superglobals())
			->array($parser->getValues())->isEmpty()
			->array($parser->getHandlers())->isEmpty()
			->object($parser->getIterator())->isEmpty()
		;

		$parser = new script\arguments\parser($superglobals = new atoum\superglobals());

		$this->assert
			->object($parser->getSuperGlobals())->isIdenticalTo($superglobals)
			->array($parser->getValues())->isEmpty()
			->array($parser->getHandlers())->isEmpty()
			->object($parser->getIterator())->isEmpty()
		;
	}

	public function testSetSuperglobals()
	{
		$parser = new script\arguments\parser();

		$this->assert
			->object($parser->setSuperglobals($superglobals = new atoum\superglobals()))->isIdenticalTo($parser)
			->object($parser->getSuperGlobals())->isIdenticalTo($superglobals)
		;
	}

	public function testGetValues()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new \mock\mageekguy\atoum\script(uniqid());

		$parser = new script\arguments\parser();

		$this->assert
			->array($parser->getValues())->isEmpty()
			->variable($parser->getValues(uniqid()))->isNull()
			->when(function() use ($parser, $script) {
					$parser
						->addHandler(function($script, $argument, $value) {}, array('-a'))
						->parse($script, array('-a'))
					;
				}
			)
				->array($parser->getValues())->isEqualTo(array('-a' => array()))
				->array($parser->getValues('-a'))->isEmpty()
				->variable($parser->getValues(uniqid()))->isNull()
			->when(function() use ($parser, $script) {
					$parser->parse($script, array('-a', 'a1', 'a2'));
				}
			)
				->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
				->array($parser->getValues('-a'))->isEqualTo(array('a1', 'a2'))
				->variable($parser->getValues(uniqid()))->isNull()
			->when(function() use ($parser, $script) {
					$parser
						->addHandler(function($script, $argument, $value) {}, array('-b'))
						->parse($script, array('-a', 'a1', 'a2', '-b'))
					;
				}
			)
				->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
				->array($parser->getValues('-a'))->isEqualTo(array('a1', 'a2'))
				->array($parser->getValues('-b'))->isEmpty()
				->variable($parser->getValues(uniqid()))->isNull()
		;
	}

	public function testGetIterator()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new \mock\mageekguy\atoum\script(uniqid());

		$parser = new script\arguments\parser();

		$this->assert
			->when(function() use ($parser, $script) {
					$parser->parse($script, array());
				}
			)
				->object($parser->getIterator())
					->isInstanceOf('arrayIterator')
					->isEmpty()
			->when(function() use ($parser, $script) {
					$parser
						->addHandler(function($script, $argument, $value) {}, array('-a'))
						->addHandler(function($script, $argument, $value) {}, array('-b'))
						->parse($script, array('-a', 'a1', 'a2', '-b'))
					;
				}
			)
				->object($parser->getIterator())
					->isInstanceOf('arrayIterator')
					->isEqualTo(new \arrayIterator($parser->getValues()))
		;
	}

	public function testParse()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new \mock\mageekguy\atoum\script(uniqid());

		$superglobals = new atoum\superglobals();
		$superglobals->_SERVER['argv'] = array();

		$parser = new script\arguments\parser($superglobals);

		$this->assert('When using $_SERVER')
			->object($parser->parse($script))->isIdenticalTo($parser)
			->array($parser->getValues())->isEmpty()
			->when(function() use ($superglobals) {
					$superglobals->_SERVER['argv'] = array('scriptName');
				}
			)
				->object($parser->parse($script))->isIdenticalTo($parser)
				->array($parser->getValues())->isEmpty()
			->when(function() use ($superglobals, $parser, & $invokeA) {
					$superglobals->_SERVER['argv'] = array('scriptName', '-a');
					$parser->addHandler(function($script, $argument, $value) use (& $invokeA) { $invokeA++; }, array('-a'));
				}
			)
				->object($parser->parse($script))->isIdenticalTo($parser)
				->array($parser->getValues())->isEqualTo(array('-a' => array()))
				->integer($invokeA)->isEqualTo(1)
			->when(function() use ($superglobals, $parser, & $invokeB) {
					$superglobals->_SERVER['argv'] = array('scriptName', '-a', '-b');
					$parser->addHandler(function($script, $argument, $value) use (& $invokeB) { $invokeB++; }, array('-b'));
				}
			)
				->object($parser->parse($script))->isIdenticalTo($parser)
				->array($parser->getValues())->isEqualTo(array('-a' => array(), '-b' => array()))
				->integer($invokeA)->isEqualTo(2)
				->integer($invokeB)->isEqualTo(1)
			->when(function() use ($superglobals) {
					$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1');
				}
			)
				->object($parser->parse($script))->isIdenticalTo($parser)
				->array($parser->getValues())->isEqualTo(array('-a' => array('a1')))
				->integer($invokeA)->isEqualTo(3)
				->integer($invokeB)->isEqualTo(1)
			->when(function() use ($superglobals) {
					$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2');
				}
			)
				->object($parser->parse($script))->isIdenticalTo($parser)
				->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
				->integer($invokeA)->isEqualTo(4)
				->integer($invokeB)->isEqualTo(1)
			->when(function() use ($superglobals) {
					$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b');
				}
			)
				->object($parser->parse($script))->isIdenticalTo($parser)
				->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
				->integer($invokeA)->isEqualTo(5)
				->integer($invokeB)->isEqualTo(2)
			->when(function() use ($superglobals) {
					$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3');
				}
			)
				->object($parser->parse($script))->isIdenticalTo($parser)
				->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3')))
				->integer($invokeA)->isEqualTo(6)
				->integer($invokeB)->isEqualTo(3)
			->when(function() use ($superglobals, $parser, & $invokeC) {
					$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c');
					$parser->addHandler(function($script, $argument, $value) use (& $invokeC) { $invokeC++; }, array('--c'));
				}
			)
				->object($parser->parse($script))->isIdenticalTo($parser)
				->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3'), '--c' => array()))
				->integer($invokeA)->isEqualTo(7)
				->integer($invokeB)->isEqualTo(4)
				->integer($invokeC)->isEqualTo(1)
		;

		$this->assert('When using argument')
			->when(function() use ($superglobals) {
					$superglobals->_SERVER['argv'] = array();
				}
			)
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
		$parser = new script\arguments\parser();

		$this->assert
			->object($parser->addHandler($handler = function($script, $argument, $values) {}, $arguments = array($argument = '-a')))->isIdenticalTo($parser)
			->array($parser->getHandlers())->isEqualTo(array($argument => array($handler)))
			->object($parser->addHandler($handler, $arguments))->isIdenticalTo($parser)
			->array($parser->getHandlers())->isEqualTo(array($argument => array($handler, $handler)))
			->exception(function() use ($parser, & $argument) {
						$parser->addHandler(function() {}, $argument = array('-b'));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Handler must take three arguments')
			->exception(function() use ($parser, & $argument) {
						$parser->addHandler(function($script) {}, array('-b'));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Handler must take three arguments')
			->exception(function() use ($parser, & $argument) {
						$parser->addHandler(function($script, $argument) {}, array('-b'));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Handler must take three arguments')
			->exception(function() use ($parser, & $argument) {
						$parser->addHandler(function($script, $argument, $values) {}, array($argument = 'b'));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Argument \'' . $argument . '\' is invalid')
		;
	}
}

?>
