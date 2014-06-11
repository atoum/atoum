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
		$this
			->if($this->newTestedInstance($name = uniqid()))
			->then
				->string($this->testedInstance->getName())->isEqualTo($name)
		;
	}

	public function testSetDefaultValue()
	{
		$this
			->if($this->newTestedInstance(uniqid()))
			->then
				->object($this->testedInstance->setDefaultValue($default = uniqid()))->isTestedInstance
		;
	}

	public function testIsReference()
	{
		$this
			->if($this->newTestedInstance(uniqid()))
			->then
				->object($this->testedInstance->isReference())->isTestedInstance
		;
	}

	public function test__toString()
	{
		$this
			->if($this->newTestedInstance($name = uniqid()))
			->then
				->castToString($this->testedInstance)->isEqualTo('$' . $name)
				->castToString($this->testedInstance->isArray())->isEqualTo('array $' . $name)
				->castToString($this->testedInstance->isObject($type = uniqid()))->isEqualTo($type . ' $' . $name)
				->castToString($this->testedInstance->isUntyped()->setDefaultValue(__FUNCTION__))->isEqualTo('$' . $name . '=' . var_export(__FUNCTION__, true))
				->castToString($this->testedInstance->setDefaultValue($defaultValue = uniqid()))->isEqualTo('$' . $name . '=' . var_export($defaultValue, true))
				->castToString($this->testedInstance->setDefaultValue(array()))->isEqualTo('$' . $name . '=' . var_export(array(), true))
				->castToString($this->testedInstance->setDefaultValue(null))->isEqualTo('$' . $name . '=' . var_export(null, true))
			->if(
				$this->newTestedInstance($name = uniqid()),
				$this->testedInstance->isReference()
			)
			->then
				->castToString($this->testedInstance)->isEqualTo('& $' . $name)
				->castToString($this->testedInstance->setDefaultValue(__FUNCTION__))->isEqualTo('& $' . $name . '=' . var_export(__FUNCTION__, true))
				->castToString($this->testedInstance->setDefaultValue($defaultValue = uniqid()))->isEqualTo('& $' . $name . '=' . var_export($defaultValue, true))
				->castToString($this->testedInstance->setDefaultValue(array()))->isEqualTo('& $' . $name . '=' . var_export(array(), true))
				->castToString($this->testedInstance->setDefaultValue(null))->isEqualTo('& $' . $name . '=' . var_export(null, true))
		;
	}
}
