<?php

namespace mageekguy\atoum\tests\units\fcgi\records\requests;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi,
	mageekguy\atoum\fcgi\records\requests\stdin as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class stdin extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\fcgi\records\request');
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::type)->isEqualTo(5)
		;
	}

	public function test__construct()
	{
		$this
			->if($record = new testedClass())
			->then
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRequestId())->isEqualTo(1)
				->string($record->getContentData())->isEmpty()
			->if($record = new testedClass($contentData = uniqid()))
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRequestId())->isEqualTo(1)
				->string($record->getContentData())->isEqualTo($contentData)
			->if($record = new testedClass($contentData = uniqid(), $requestId = rand(1, 65535)))
				->string($record->getType())->isEqualTo(testedClass::type)
				->string($record->getRequestId())->isEqualTo($requestId)
				->string($record->getContentData())->isEqualTo($contentData)
			->exception(function() { new testedClass($contentData = uniqid(), rand(- PHP_INT_MAX, 0)); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Request ID must be greater than 0')
			->exception(function() { new testedClass($contentData = uniqid(), ''); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Request ID must be greater than 0')
		;
	}

	public function testSetContentData()
	{
		$this
			->if($record = new testedClass())
			->then
				->object($record->setContentData($contentData = uniqid()))->isIdenticalTo($record)
				->string($record->getContentData())->isEqualTo($contentData)
		;
	}
}
