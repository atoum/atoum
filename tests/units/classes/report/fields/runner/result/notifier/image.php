<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\exceptions,
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
				->object($field->setSuccessImage($path = uniqid()))->isIdenticalTo($field)
				->string($field->getSuccessImage())->isEqualTo($path)
		;
	}

	public function testGetSetFailureImage()
	{
		$this
			->if($adapter = new adapter())
			->and($adapter->file_exists = true)
			->and($field = new \mock\mageekguy\atoum\report\fields\runner\result\notifier\image($adapter))
			->then
				->variable($field->getFailureImage())->isNull()
				->object($field->setFailureImage($path = uniqid()))->isIdenticalTo($field)
				->string($field->getFailureImage())->isEqualTo($path)
			->if($adapter->file_exists = false)
			->then
				->object($field->setFailureImage($path = uniqid()))->isIdenticalTo($field)
				->string($field->getFailureImage())->isEqualTo($path)
		;
	}

	public function testGetImage()
	{
		$this
			->if($adapter = new adapter())
			->and($adapter->file_exists = true)
			->and($field = new \mock\mageekguy\atoum\report\fields\runner\result\notifier\image($adapter))
			->and($field->setSuccessImage($successImage = uniqid()))
			->then
				->string($field->getImage(true))->isEqualTo($successImage)
			->if($adapter->file_exists = false)
			->then
				->exception(function() use($field) {
							$field->getImage(true);
						}
					)
						->isInstanceOf('\\mageekguy\\atoum\\exceptions\\runtime')
						->hasMessage(sprintf('File %s does not exist', $successImage))
			->if($field->setFailureImage($failureImage = uniqid()))
			->and($adapter->file_exists = true)
			->then
				->string($field->getImage(false))->isEqualTo($failureImage)
			->if($adapter->file_exists = false)
			->then
				->exception(function() use($field) {
							$field->getImage(false);
						}
					)
						->isInstanceOf('\\mageekguy\\atoum\\exceptions\\runtime')
						->hasMessage(sprintf('File %s does not exist', $failureImage))
		;
	}

	public function testAsString()
	{
		$this
			->if($field = new \mock\mageekguy\atoum\report\fields\runner\result\notifier\image())
			->and($this->calling($field)->notify = null)
			->then
				->castToString($field)->isEmpty()
			->if($this->calling($field)->notify = $output = uniqid())
			->then
				->castToString($field)->isEqualTo($output . PHP_EOL)
			->if($field = new \mock\mageekguy\atoum\report\fields\runner\result\notifier\image())
			->and($this->calling($field)->notify->throw = new exceptions\runtime($message = uniqid()))
			->then
				->castToString($field)->isEqualTo($message . PHP_EOL)
		;
	}
}
