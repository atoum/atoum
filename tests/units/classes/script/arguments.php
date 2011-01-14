<?php

namespace mageekguy\atoum\tests\units\script;

use mageekguy\atoum;
use mageekguy\atoum\script;

require_once(__DIR__ . '/../runner.php');

class arguments extends atoum\test
{
	public function test__construct()
	{
		$arguments = new script\arguments();

		$this->assert
			->array($arguments->getValues())->isEmpty()
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
}

?>
