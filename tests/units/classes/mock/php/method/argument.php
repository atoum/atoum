<?php

namespace mageekguy\atoum\tests\units\mock\php\method;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\php
;

require_once __DIR__ . '/../../../../runner.php';

class argument extends atoum\test
{
	public function test__construct()
	{
		$argument = new php\method\argument($name = uniqid());

		$this->assert
			->string($argument->getName())->isEqualTo($name)
		;
	}

	public function testSetDefaultValue()
	{
		$argument = new php\method\argument(uniqid());

		$this->assert
			->object($argument->setDefaultValue($default = uniqid()))->isIdenticalTo($argument)
		;
	}

	public function testIsReference()
	{
		$argument = new php\method\argument(uniqid());

		$this->assert
			->object($argument->isReference())->isIdenticalTo($argument)
		;
	}

	public function test__toString()
	{
		$argument = new php\method\argument($name = uniqid());

		$this->assert
			->castToString($argument)->isEqualTo('$' . $name)
			->castToString($argument->isArray())->isEqualTo('array $' . $name)
			->castToString($argument->isObject($type = uniqid()))->isEqualTo($type . ' $' . $name)
			->castToString($argument->isUntyped()->setDefaultValue(__FUNCTION__))->isEqualTo('$' . $name . '=' . var_export(__FUNCTION__, true))
			->castToString($argument->setDefaultValue($defaultValue = uniqid()))->isEqualTo('$' . $name . '=' . var_export($defaultValue, true))
			->castToString($argument->setDefaultValue(array()))->isEqualTo('$' . $name . '=' . var_export(array(), true))
			->castToString($argument->setDefaultValue(null))->isEqualTo('$' . $name . '=' . var_export(null, true))
		;

		$argument = new php\method\argument($name = uniqid());

		$argument->isReference();

		$this->assert
			->castToString($argument)->isEqualTo('& $' . $name)
			->castToString($argument->setDefaultValue(__FUNCTION__))->isEqualTo('& $' . $name . '=' . var_export(__FUNCTION__, true))
			->castToString($argument->setDefaultValue($defaultValue = uniqid()))->isEqualTo('& $' . $name . '=' . var_export($defaultValue, true))
			->castToString($argument->setDefaultValue(array()))->isEqualTo('& $' . $name . '=' . var_export(array(), true))
			->castToString($argument->setDefaultValue(null))->isEqualTo('& $' . $name . '=' . var_export(null, true))
		;
	}
}
