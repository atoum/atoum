<?php

namespace mageekguy\atoum\tests\units\autoloader;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class mock extends atoum\test
{
	public function testGetSetMockGenerator()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->setMockGenerator())->isTestedInstance
				->object($this->testedInstance->getMockGenerator())->isInstanceOf('mageekguy\atoum\mock\generator')
				->object($this->testedInstance->setMockGenerator($generator = new \mock\mageekguy\atoum\mock\generator))->isTestedInstance
				->object($this->testedInstance->getMockGenerator())->isIdenticalTo($generator)
		;
	}

	public function testGetSetAdapter()
	{
		$this
			->given($this->newTestedInstance)
			->then
				->object($this->testedInstance->setAdapter())->isTestedInstance
				->object($this->testedInstance->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($this->testedInstance->setAdapter($adapter = new \mock\mageekguy\atoum\adapter))->isTestedInstance
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testRegister()
	{
		$this
			->given(
				$adapter = new atoum\test\adapter,
				$this->newTestedInstance(null, $adapter)
			)
			->if($adapter->spl_autoload_register = false)
			->then
				->exception(function($test) {
						$test->testedInstance->register();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to register mock autoloader')
				->adapter($adapter)
					->call('spl_autoload_register')->withArguments(array($this->testedInstance, 'requireClass'))->once
			->if($adapter->spl_autoload_register = true)
			->then
				->object($this->testedInstance->register())->isTestedInstance
				->adapter($adapter)
					->call('spl_autoload_register')->withArguments(array($this->testedInstance, 'requireClass'))->twice
		;
	}

	public function testUnregister()
	{
		$this
			->given(
				$adapter = new atoum\test\adapter,
				$this->newTestedInstance(null, $adapter)
			)
			->if($adapter->spl_autoload_unregister = false)
			->then
				->exception(function($test) {
						$test->testedInstance->unregister();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to unregister mock autoloader')
				->adapter($adapter)
					->call('spl_autoload_unregister')->withArguments(array($this->testedInstance, 'requireClass'))->once
			->if($adapter->spl_autoload_unregister = true)
			->then
				->object($this->testedInstance->unregister())->isTestedInstance
				->adapter($adapter)
					->call('spl_autoload_unregister')->withArguments(array($this->testedInstance, 'requireClass'))->twice
		;
	}

	public function testRequireClass()
	{
		$this
			->given(
				$generator = new \mock\mageekguy\atoum\mock\generator,
				$this->newTestedInstance($generator)
			)
			->if($this->calling($generator)->generate->doesNothing)
			->then
				->object($this->testedInstance->requireClass($class = uniqid()))->isTestedInstance
				->mock($generator)
					->call('generate')->never
			->if($this->calling($generator)->getDefaultNamespace = $namespace = uniqid())
			->then
				->object($this->testedInstance->requireClass($namespace . '\\' . $class))->isTestedInstance
				->mock($generator)
					->call('generate')->withArguments($class)->once
			->if($this->calling($generator)->getDefaultNamespace = $namespace = '\\' . uniqid())
			->then
				->object($this->testedInstance->requireClass($namespace . '\\' . $class))->isTestedInstance
				->mock($generator)
					->call('generate')->withArguments($class)->twice
				->object($this->testedInstance->requireClass(ltrim($namespace, '\\') . '\\' . $class))->isTestedInstance
				->mock($generator)
					->call('generate')->withArguments($class)->thrice
		;
	}
} 
