<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\report\fields\runner\result\notifier\image as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class image extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->extends('mageekguy\atoum\report\fields\runner\result\notifier')
		;
	}

	public function testGetSetDirectory()
	{
		$this
			->if($adapter = new adapter())
			->if($adapter->is_dir = true)
			->and($field = new \mock\mageekguy\atoum\report\fields\runner\result\notifier\image($adapter))
			->then
				->variable($field->getDirectory())->isNull()
				->object($field->setDirectory($directory = uniqid()))->isIdenticalTo($field)
				->string($field->getDirectory())->isEqualTo($directory)
			->if($adapter->is_dir = false)
			->then
				->exception(function() use(& $directory, $field) {
							$field->setDirectory($directory = uniqid());
						}
					)
						->isInstanceOf('\\mageekguy\\atoum\\exceptions\\logic\\invalidArgument')
						->hasMessage(sprintf('Directory %s does not exist', $directory))
		;
	}
}
