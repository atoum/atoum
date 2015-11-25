<?php

namespace mageekguy\atoum\tests\units\php\mocker;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\php
;

class constant extends atoum\test
{
	public function test__set()
	{
		$this
			->given(
				$this->newTestedInstance,
				$adapter = new atoum\test\adapter(),
				php\mocker\constant::setAdapter($adapter)
			)

			->if(
				$adapter->define = true,
				$this->testedInstance->setDefaultNameSpace($namespace = uniqid())
			)
			->then
				->string($this->testedInstance->{$constant = uniqid()} = $value = uniqid())->isEqualTo($value)
				->adapter($adapter)
					->call('define')->withArguments($namespace . '\\' . $constant, $value)->once

			->if($adapter->define = false)
			->then
				->exception(function(atoum\test $test) use (& $constant, & $value) {
						$test->testedInstance->{$constant = uniqid('a')} = $value = uniqid();
					}
				)
					->isInstanceOf('mageekguy\atoum\php\mocker\exceptions\constant')
					->hasMessage('Could not mock constant \'' . $constant . '\' in namespace \'' . $namespace . '\'')
				->adapter($adapter)
					->call('define')->withArguments($namespace . '\\' . $constant, $value)->once
		;
	}

	public function test__get()
	{
		$this
			->given(
				$this->newTestedInstance,
				$adapter = new atoum\test\adapter(),
				php\mocker\constant::setAdapter($adapter)
			)

			->if(
				$adapter->defined = false,
				$this->testedInstance->setDefaultNameSpace($namespace = uniqid())
			)
			->then
				->exception(function(atoum\test $test) use (& $constant) {
						$test->testedInstance->{$constant = uniqid()};
					}
				)
					->isInstanceOf('mageekguy\atoum\php\mocker\exceptions\constant')
					->hasMessage('Constant \'' . $constant . '\' is not defined in namespace \'' . $namespace . '\'')
				->adapter($adapter)
					->call('defined')->withArguments($namespace . '\\' . $constant)->once

			->if(
				$adapter->defined = true,
				$adapter->constant = $value = uniqid()
			)
			->then
				->string($this->testedInstance->{$constant = uniqid()})->isEqualTo($value)
				->adapter($adapter)
					->call('defined')->withArguments($namespace . '\\' . $constant)->once
					->call('constant')->withArguments($namespace . '\\' . $constant)->once
		;
	}

	public function test__isset()
	{
		$this
			->given(
				$this->newTestedInstance,
				$adapter = new atoum\test\adapter(),
				php\mocker\constant::setAdapter($adapter)
			)

			->if(
				$adapter->defined = false,
				$this->testedInstance->setDefaultNameSpace($namespace = uniqid())
			)
			->then
				->boolean(isset($this->testedInstance->{$constant = uniqid()}))->isFalse
				->adapter($adapter)
					->call('defined')->withArguments($namespace . '\\' . $constant)->once

			->if($adapter->defined = true)
			->then
				->boolean(isset($this->testedInstance->{$constant = uniqid()}))->isTrue
				->adapter($adapter)
					->call('defined')->withArguments($namespace . '\\' . $constant)->once
		;
	}

	public function test__unset()
	{
		$this
			->given(
				$this->newTestedInstance,
				$adapter = new atoum\test\adapter()
			)

			->if($this->testedInstance->setDefaultNameSpace($namespace = uniqid()))
			->then
				->exception(function(atoum\test $test) use (& $constant, & $value) {
						unset($test->testedInstance->{$constant = uniqid()});
					}
				)
					->isInstanceOf('mageekguy\atoum\php\mocker\exceptions\constant')
					->hasMessage('Could not unset constant \'' . $constant . '\' in namespace \'' . $namespace . '\'')
		;
	}
}
