<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

class registry extends atoum\test
{
	public function testGetInstance()
	{
		$registry = atoum\registry::getInstance();

		$this->assert
			->object($registry)->isInstanceOf('\mageekguy\atoum\registry')
			->isIdenticalTo(atoum\registry::getInstance())
		;
	}

	public function test__set()
	{
		$registry = atoum\registry::getInstance();

		$registry->{($key = uniqid())} = ($value = uniqid());

		$this->assert
			->string($registry->{$key})->isEqualTo($value)
			->exception(function() use ($registry, $key) {
						$registry->{$key} = uniqid();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Key \'' . $key . '\' is already in registry')
		;
	}

	public function test__get()
	{
		$registry = atoum\registry::getInstance();

		$this->assert
			->exception(function() use ($registry, & $key) {
						$registry->{$key = uniqid()};
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Key \'' . $key . '\' is not in registry')
		;
	}

	public function test__isset()
	{
		$registry = atoum\registry::getInstance();

		$this->assert
			->boolean(isset($registry->{($key = uniqid())}))->isFalse()
		;

		$registry->{$key} = uniqid();

		$this->assert
			->boolean(isset($registry->{$key}))->isTrue()
		;
	}

	public function test__unset()
	{
		$registry = atoum\registry::getInstance();

		$this->assert
			->boolean(isset($registry->{($key = uniqid())}))->isFalse()
			->exception(function() use ($registry, $key) {
						unset($registry->{$key});
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Key \'' . $key . '\' is not in registry')
		;

		$registry->{$key} = uniqid();

		$this->assert
			->boolean(isset($registry->{$key}))->isTrue()
		;

		unset($registry->{$key});

		$this->assert
			->boolean(isset($registry->{$key}))->isFalse()
		;
	}
}

?>
