<?php

namespace mageekguy\atoum\tests\units\php;

use
	mageekguy\atoum,
	mageekguy\atoum\php
;

require_once __DIR__ . '/../../runner.php';

class call extends atoum\test
{
	public function test__construct()
	{
		$call = new php\call($function = uniqid());

		$this->assert
			->string($call->getFunction())->isEqualTo($function)
			->variable($call->getArguments())->isNull()
			->variable($call->getObject())->isNull()
		;

		$call = new php\call($function = uniqid(), $arguments = array($arg = uniqid()));

		$this->assert
			->string($call->getFunction())->isEqualTo($function)
			->array($call->getArguments())->isEqualTo($arguments)
			->variable($call->getObject())->isNull()
		;

		$call = new php\call($function = uniqid(), $arguments = array($arg = uniqid()), $object = $this);

		$this->assert
			->string($call->getFunction())->isEqualTo($function)
			->array($call->getArguments())->isEqualTo($arguments)
			->object($call->getObject())->isIdenticalTo($object)
		;
	}

	public function testSetFunction()
	{
		$call = new php\call(uniqid());

		$this->assert
			->object($call->setFunction($function = uniqid()))->isIdenticalTo($call)
			->string($call->getFunction())->isEqualTo($function)
			->object($call->setFunction($function = uniqid()))->isIdenticalTo($call)
			->string($call->getFunction())->isEqualTo($function)
		;
	}

	public function testSetArguments()
	{
		$call = new php\call(uniqid());

		$this->assert
			->object($call->setArguments($args = array(uniqid())))->isIdenticalTo($call)
			->array($call->getArguments())->isEqualTo($args)
			->object($call->setArguments($args = array(uniqid())))->isIdenticalTo($call)
			->array($call->getArguments())->isEqualTo($args)
		;
	}

	public function testUnsetArguments()
	{
		$call = new php\call(uniqid());

		$this->assert
			->variable($call->getArguments())->isNull()
			->object($call->unsetArguments())->isIdenticalTo($call)
			->variable($call->getArguments())->isNull()
		;

		$call->setArguments(array(uniqid()));

		$this->assert
			->variable($call->getArguments())->isNotNull()
			->object($call->unsetArguments())->isIdenticalTo($call)
			->variable($call->getArguments())->isNull()
		;
	}

	public function testSetObject()
	{
		$call = new php\call(uniqid());

		$this->assert
			->object($call->setObject($object = $this))->isIdenticalTo($call)
			->object($call->getObject())->isIdenticalTo($object)
			->object($call->setObject($object = $call))->isIdenticalTo($call)
			->object($call->getObject())->isIdenticalTo($object)
		;
	}

	public function testSetDecorator()
	{
		$call = new php\call(uniqid());

		$this->assert
			->object($call->setDecorator($decorator = new php\call\decorator()))->isIdenticalTo($call)
			->object($call->getDecorator())->isIdenticalTo($decorator)
		;
	}

	public function test__toString()
	{
		$call = new php\call($function = uniqid());

		$this->assert
			->castToString($call)->isEqualTo($call->getDecorator()->decorate($call))
		;
	}
}
