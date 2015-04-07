<?php

namespace mageekguy\atoum\tests\units\php;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class extension extends atoum\test
{
	public function testIsLoaded()
	{
		$this
			->given($this->newTestedInstance(uniqid()))
			->if($this->function->extension_loaded = false)
			->then
				->boolean($this->testedInstance->isLoaded())->isFalse
			->if($this->function->extension_loaded = true)
			->then
				->boolean($this->testedInstance->isLoaded())->isTrue
		;
	}

	public function testRequireExtension()
	{
		$this
			->given($this->newTestedInstance($extensionName = uniqid()))
			->if($this->function->extension_loaded = false)
			->then
				->exception(function($test) {
						$test->testedInstance->requireExtension();
					}
				)
					->isInstanceOf('mageekguy\atoum\php\exception')
					->hasMessage('PHP extension \'' . $extensionName . '\' is not loaded')
			->if($this->function->extension_loaded = true)
			->then
				->object($this->testedInstance->requireExtension())->isTestedInstance
		;
	}
} 
