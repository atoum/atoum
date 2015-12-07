<?php

namespace mageekguy\atoum\tests\units\test\data\providers;

use mageekguy\atoum;

require_once __DIR__ . '/../../../runner.php';

class dummy2 {
	private function __construct() {}
}

class mock extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->implements('mageekguy\atoum\test\data\provider');
	}

	public function test__construct(atoum\mock\generator $mockGenerator)
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->getMockGenerator())->isInstanceOf('mageekguy\atoum\mock\generator')
			->if($this->testedInstance->setMockGenerator($mockGenerator))
			->then
				->object($this->testedInstance->getMockGenerator())->isIdenticalTo($mockGenerator)
		;
	}

	public function testGenerate(atoum\mock\generator $mockGenerator)
	{
		$this
			->given(
				$this->calling($mockGenerator)->getDefaultNamespace = $namespace = uniqid('a'),
				$this->newTestedInstance($mockGenerator)
			)
			->then
				->exception(function($test) {
						$test->testedInstance->generate();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Class is undefined')
			->given($class = 'stdClass')
			->if($this->testedInstance->setClass($class))
			->then
				->object($this->testedInstance->generate())->isInstanceOf($class)
				->object($this->testedInstance->generate())->isInstanceOf($namespace . '\\' . $class)

			->assert('Fail to instanciate an object from a class with mandatory arguments')
			->given($class = 'splFileObject')
			->if($this->testedInstance->setClass($class))
			->then
				->exception(function($test) {
						$test->testedInstance->generate();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Could not instanciate a mock from ' . $namespace . '\\' . $class . ' because ' . $class . '::__construct() has at least one mandatory argument')

			->assert('Instanciate an object from a class with a private constructor')
			->given($class = __NAMESPACE__ . '\\dummy2')
			->if($this->testedInstance->setClass($class))
			->then
				->object($this->testedInstance->generate())->isInstanceOf($class)
				->object($this->testedInstance->generate())->isInstanceOf($namespace . '\\' . $class)
		;
	}

	public function testGetSetClass(atoum\mock\generator $mockGenerator)
	{
		$this
			->given($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getClass())->isNull
				->exception(function($test) {
						$test->testedInstance->setClass(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument must be a class name')
			->given($class = 'stdClass')
			->if($this->calling($mockGenerator)->getDefaultNamespace = $namespace = uniqid())
			->then
				->object($this->testedInstance->setClass($class))->istestedInstance
		;
	}
}
