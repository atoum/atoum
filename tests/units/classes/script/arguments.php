<?php

namespace mageekguy\atoum\tests\units\script;

use mageekguy\atoum;
use mageekguy\atoum\script;

require_once(__DIR__ . '/../../runner.php');

class arguments extends atoum\test
{
	public function test__construct()
	{
		$arguments = new script\arguments();

		$this->assert
			->object($arguments)->isInstanceOf('\iteratorAggregate')
			->array($arguments->getValues())->isEmpty()
			->array($arguments->getHandlers())->isEmpty()
		;
	}

	public function testGetValues()
	{
		$arguments = new script\arguments();

		$this->assert
			->array($arguments->getValues())->isEmpty()
			->variable($arguments->getValues(uniqid()))->isNull()
		;

		$arguments->parse(array('-a'));

		$this->assert
			->array($arguments->getValues())->isEqualTo(array('-a' => array()))
			->array($arguments->getValues('-a'))->isEmpty()
			->variable($arguments->getValues(uniqid()))->isNull()
		;

		$arguments->parse(array('-a', 'a1', 'a2'));

		$this->assert
			->array($arguments->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
			->array($arguments->getValues('-a'))->isEqualTo(array('a1', 'a2'))
			->variable($arguments->getValues(uniqid()))->isNull()
		;

		$arguments->parse(array('-a', 'a1', 'a2', '-b'));

		$this->assert
			->array($arguments->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
			->array($arguments->getValues('-a'))->isEqualTo(array('a1', 'a2'))
			->array($arguments->getValues('-b'))->isEmpty()
			->variable($arguments->getValues(uniqid()))->isNull()
		;
	}

	public function testGetIterator()
	{
		$arguments = new script\arguments();

		$this->assert
			->object($arguments->getIterator())->isInstanceOf('\arrayIterator')
			->isEmpty()
		;

		$arguments->parse(array());

		$this->assert
			->object($arguments->getIterator())->isInstanceOf('\arrayIterator')
			->isEmpty()
		;

		$arguments->parse(array('-a', 'a1', 'a2', '-b'));

		$this->assert
			->object($arguments->getIterator())->isInstanceOf('\arrayIterator')
			->isEqualTo(new \arrayIterator($arguments->getValues()))
		;
	}

	public function testParse()
	{
		$arguments = new script\arguments();

		$this->assert
			->object($arguments->parse(array()))->isIdenticalTo($arguments)
			->array($arguments->getValues())->isEmpty()
			->exception(function() use ($arguments) {
					$arguments->parse(array('b'));
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime\unexpectedValue')
				->hasMessage('First argument is invalid')
			->object($arguments->parse(array('-a')))->isIdenticalTo($arguments)
			->array($arguments->getValues())->isEqualTo(array('-a' => array()))
			->object($arguments->parse(array('-a', '-b')))->isIdenticalTo($arguments)
			->array($arguments->getValues())->isEqualTo(array('-a' => array(), '-b' => array()))
			->object($arguments->parse(array('-a', 'a1')))->isIdenticalTo($arguments)
			->array($arguments->getValues())->isEqualTo(array('-a' => array('a1')))
			->object($arguments->parse(array('-a', 'a1', 'a2')))->isIdenticalTo($arguments)
			->array($arguments->getValues())->isEqualTo(array('-a' => array('a1', 'a2')))
			->object($arguments->parse(array('-a', 'a1', 'a2', '-b')))->isIdenticalTo($arguments)
			->array($arguments->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array()))
			->object($arguments->parse(array('-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3')))->isIdenticalTo($arguments)
			->array($arguments->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3')))
			->object($arguments->parse(array('-a', 'a1', 'a2', '-b', 'b1', 'b2', 'b3', '--c')))->isIdenticalTo($arguments)
			->array($arguments->getValues())->isEqualTo(array('-a' => array('a1', 'a2'), '-b' => array('b1', 'b2', 'b3'), '--c' => array()))
		;

		$arguments = new script\arguments();

		$invoke = 0;

		$handler = function ($values) use (& $invoke) { $invoke++; };

		$arguments->addHandler('-a', $handler);

		$this->assert
			->object($arguments->parse(array()))->isIdenticalTo($arguments)
			->integer($invoke)->isZero()
			->object($arguments->parse(array('-a')))->isIdenticalTo($arguments)
			->integer($invoke)->isEqualTo(1)
		;

		$arguments->addHandler('-a', $handler);

		$this->assert
			->object($arguments->parse(array('-a')))->isIdenticalTo($arguments)
			->integer($invoke)->isEqualTo(3)
		;

		$arguments->addHandler('-b', $handler);

		$this->assert
			->object($arguments->parse(array('-a')))->isIdenticalTo($arguments)
			->integer($invoke)->isEqualTo(5)
			->object($arguments->parse(array('-a', '-b')))->isIdenticalTo($arguments)
			->integer($invoke)->isEqualTo(8)
		;
	}

	public function testIsArgument()
	{
		$this->assert
			->boolean(script\arguments::isArgument(uniqid()))->isFalse()
			->boolean(script\arguments::isArgument('+' . rand(0, 9)))->isFalse()
			->boolean(script\arguments::isArgument('-' . rand(0, 9)))->isFalse()
			->boolean(script\arguments::isArgument('--' . rand(0, 9)))->isFalse()
			->boolean(script\arguments::isArgument('+_'))->isFalse()
			->boolean(script\arguments::isArgument('-_'))->isFalse()
			->boolean(script\arguments::isArgument('--_'))->isFalse()
			->boolean(script\arguments::isArgument('+-'))->isFalse()
			->boolean(script\arguments::isArgument('---'))->isFalse()
			->boolean(script\arguments::isArgument('+a'))->isTrue()
			->boolean(script\arguments::isArgument('-a'))->isTrue()
			->boolean(script\arguments::isArgument('--a'))->isTrue()
			->boolean(script\arguments::isArgument('+a' . rand(0, 9)))->isTrue()
			->boolean(script\arguments::isArgument('-a' . rand(0, 9)))->isTrue()
			->boolean(script\arguments::isArgument('--a' . rand(0, 9)))->isTrue()
			->boolean(script\arguments::isArgument('+aa'))->isTrue()
			->boolean(script\arguments::isArgument('-aa'))->isTrue()
			->boolean(script\arguments::isArgument('--aa'))->isTrue()
		;
	}

	public function testAddHandler()
	{
		$arguments = new script\arguments();

		$this->assert
			->object($arguments->addHandler($argument = '-a', $handler = function($values) {}), $arguments)
			->array($arguments->getHandlers())->isEqualTo(array($argument => array($handler)))
			->object($arguments->addHandler($argument, $handler), $arguments)
			->array($arguments->getHandlers())->isEqualTo(array($argument => array($handler, $handler)))
			->exception(function() use ($arguments, & $argument) {
						$arguments->addHandler($argument = '-b', function() {});
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
					->hasMessage('Handler of argument \'' . $argument . '\' must take one argument')
			->exception(function() use ($arguments, & $argument) {
						$arguments->addHandler($argument = uniqid(), function($values) {});
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
					->hasMessage('Argument \'' . $argument . '\' is invalid')
		;
	}
}

?>
