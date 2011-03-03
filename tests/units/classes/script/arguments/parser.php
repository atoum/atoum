<?php

namespace mageekguy\atoum\tests\units\script\arguments;

use mageekguy\atoum;
use mageekguy\atoum\mock;
use mageekguy\atoum\script;

require_once(__DIR__ . '/../../../runner.php');

class parser extends atoum\test
{
	public function test__construct()
	{
		$parser = new script\arguments\parser();

		$this->assert
			->object($parser)->isInstanceOf('\iteratorAggregate')
			->object($parser->getSuperGlobals())->isInstanceOf('\mageekguy\atoum\superglobals')
			->array($parser->getValues())->isEmpty()
			->array($parser->getHandlers())->isEmpty()
		;

		$parser = new script\arguments\parser($superglobals = new atoum\superglobals());

		$this->assert
			->object($parser->getSuperGlobals())->isIdenticalTo($superglobals)
		;
	}

	public function testSetScript()
	{
		$parser = new script\arguments\parser();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\script');

		$this->assert
			->object($parser->setScript($script = new mock\mageekguy\atoum\script(uniqid())))->isIdenticalTo($parser)
			->object($parser->getScript())->isIdenticalTo($script)
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
		$parser = new script\arguments\parser();

		$this->assert
			->array($parser->getValues())->isEmpty()
			->variable($parser->getValues(uniqid()))->isNull()
		;

		$parser->parse(array('-a'));

		$this->assert
			->array($parser->getValues())->isEqualTo(array('-a' => array()))
			->array($parser->getValues('-a'))->isEmpty()
			->variable($parser->getValues(uniqid()))->isNull()
		;

		$parser->parse(array('-a', 'a1', 'a2'));

		$this->assert
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
			->array($parser->getValues('-a'))->isEqualTo(array('a1', 'a2'))
			->variable($parser->getValues(uniqid()))->isNull()
		;

		$parser->parse(array('-a', 'a1', 'a2', '-b'));

		$this->assert
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
			->array($parser->getValues('-a'))->isEqualTo(array('a1', 'a2'))
			->array($parser->getValues('-b'))->isEmpty()
			->variable($parser->getValues(uniqid()))->isNull()
		;
	}

	public function testGetIterator()
	{
		$parser = new script\arguments\parser();

		$this->assert
			->object($parser->getIterator())->isInstanceOf('\arrayIterator')
			->isEmpty()
		;

		$parser->parse(array());

		$this->assert
			->object($parser->getIterator())->isInstanceOf('\arrayIterator')
			->isEmpty()
		;

		$parser->parse(array('-a', 'a1', 'a2', '-b'));

		$this->assert
			->object($parser->getIterator())->isInstanceOf('\arrayIterator')
			->isEqualTo(new \arrayIterator($parser->getValues()))
		;
	}

	public function testParse()
	{
		$superglobals = new atoum\superglobals();

		$superglobals->_SERVER['argv'] = array();

		$parser = new script\arguments\parser($superglobals);

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEmpty()
		;

		$superglobals->_SERVER['argv'] = array('scriptName');

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEmpty()
		;

		$superglobals->_SERVER['argv'] = array('scriptName', '-a');

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array()))
		;

		$superglobals->_SERVER['argv'] = array('scriptName', '-a', '-b');

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array(), '-b' => array()))
		;

		$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1');

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1')))
		;

		$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2');

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
		;

		$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b');

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
		;

		$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3');

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3')))
		;

		$superglobals->_SERVER['argv'] = array('scriptName', '-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c');

		$this->assert
			->object($parser->parse())->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3'), '--c' => array()))
		;

		$this->assert
			->object($parser->parse(array()))->isIdenticalTo($parser)
			->array($parser->getValues())->isEmpty()
			->exception(function() use ($parser) {
					$parser->parse(array('b'));
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime\unexpectedValue')
				->hasMessage('First argument is invalid')
			->object($parser->parse(array('-a')))->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array()))
			->object($parser->parse(array('-a', '-b')))->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array(), '-b' => array()))
			->object($parser->parse(array('-a', 'a1')))->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1')))
			->object($parser->parse(array('-a', 'a1', 'a2')))->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
			->object($parser->parse(array('-a', 'a1', 'a2', '-b')))->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
			->object($parser->parse(array('-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3')))->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3')))
			->object($parser->parse(array('-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c')))->isIdenticalTo($parser)
			->array($parser->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3'), '--c' => array()))
		;

		$parser = new script\arguments\parser();

		$invoke = 0;

		$handler = function ($script, $argument, $values) use (& $invoke) { $invoke++; };

		$parser->addHandler($handler, array('-a'));

		$this->assert
			->object($parser->parse(array()))->isIdenticalTo($parser)
			->integer($invoke)->isZero()
			->object($parser->parse(array('-a')))->isIdenticalTo($parser)
			->integer($invoke)->isEqualTo(1)
		;

		$parser->addHandler($handler, array('-a'));

		$this->assert
			->object($parser->parse(array('-a')))->isIdenticalTo($parser)
			->integer($invoke)->isEqualTo(3)
		;

		$parser->addHandler($handler, array('-b'));

		$this->assert
			->object($parser->parse(array('-a')))->isIdenticalTo($parser)
			->integer($invoke)->isEqualTo(5)
			->object($parser->parse(array('-a', '-b')))->isIdenticalTo($parser)
			->integer($invoke)->isEqualTo(8)
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
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
					->hasMessage('Handler must take three arguments')
			->exception(function() use ($parser, & $argument) {
						$parser->addHandler(function($script) {}, array('-b'));
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
					->hasMessage('Handler must take three arguments')
			->exception(function() use ($parser, & $argument) {
						$parser->addHandler(function($script, $argument) {}, array('-b'));
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
					->hasMessage('Handler must take three arguments')
			->exception(function() use ($parser, & $argument) {
						$parser->addHandler(function($script, $argument, $values) {}, array($argument = 'b'));
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
					->hasMessage('Argument \'' . $argument . '\' is invalid')
		;
	}
}

?>
