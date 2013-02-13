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

	public function testGetSetSuccessImage()
	{
		$this
			->if($adapter = new adapter())
			->if($adapter->file_exists = true)
			->and($field = new \mock\mageekguy\atoum\report\fields\runner\result\notifier\image($adapter))
			->then
			->variable($field->getSuccessImage())->isNull()
			->object($field->setSuccessImage($path = uniqid()))->isIdenticalTo($field)
			->string($field->getSuccessImage())->isEqualTo($path)
			->if($adapter->file_exists = false)
			->then
				->exception(function() use(& $path, $field) {
							$field->setSuccessImage($path = uniqid());
						}
					)
						->isInstanceOf('\\mageekguy\\atoum\\exceptions\\logic\\invalidArgument')
						->hasMessage(sprintf('File %s does not exist', $path))
		;
	}

	public function testGetSetFailureImage()
	{
		$this
			->if($adapter = new adapter())
			->if($adapter->file_exists = true)
			->and($field = new \mock\mageekguy\atoum\report\fields\runner\result\notifier\image($adapter))
			->then
			->variable($field->getFailureImage())->isNull()
			->object($field->setFailureImage($path = uniqid()))->isIdenticalTo($field)
			->string($field->getFailureImage())->isEqualTo($path)
			->if($adapter->file_exists = false)
			->then
				->exception(function() use(& $path, $field) {
							$field->setFailureImage($path = uniqid());
						}
					)
						->isInstanceOf('\\mageekguy\\atoum\\exceptions\\logic\\invalidArgument')
						->hasMessage(sprintf('File %s does not exist', $path))
		;
	}
}
