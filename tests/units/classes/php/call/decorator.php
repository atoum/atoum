<?php

namespace mageekguy\atoum\tests\units\php\call;

use
	mageekguy\atoum,
	mageekguy\atoum\php\call
;

require_once __DIR__ . '/../../../runner.php';

class decorator extends atoum\test
{
	public function test__construct()
	{
		$decorator = new call\decorator();

		$this->assert
			->object($decorator->getArgumentsDecorator())->isEqualTo(new call\arguments\decorator())
		;
	}

	public function testSetArgumentsDecorator()
	{
		$decorator = new call\decorator();

		$this->assert
			->object($decorator->setArgumentsDecorator($argumentsDecorator = new call\arguments\decorator()))->isIdenticalTo($decorator)
			->object($decorator->getArgumentsDecorator())->isIdenticalTo($argumentsDecorator)
		;
	}

	public function testDecorate()
	{
		$decorator = new call\decorator();

		$call = new call($function = uniqid());

		$this->assert
			->string($decorator->decorate($call))->isEqualTo($function . '()')
		;

		$call = new call($function = uniqid(), $args = array(uniqid()));

		$this->assert
			->string($decorator->decorate($call))->isEqualTo($function . '(' . $decorator->getArgumentsDecorator()->decorate($args) . ')')
		;

		$call = new call($function = uniqid(), $args = array(uniqid()), $this);

		$this->assert
			->string($decorator->decorate($call))->isEqualTo(get_class($this) . '::' . $function . '(' . $decorator->getArgumentsDecorator()->decorate($args) . ')')
		;
	}
}
