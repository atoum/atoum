<?php

namespace mageekguy\atoum\tests\units\fcgi\records;

use
	mageekguy\atoum,
	mock\mageekguy\atoum\fcgi\records\request as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class request extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->isSubclassOf('mageekguy\atoum\fcgi\record')
			->hasInterface('countable')
			->isAbstract()
		;
	}

	public function test__construct()
	{
		$this
			->if($record = new testedClass($type = rand(0, 255)))
			->then
				->string($record->getType())->isEqualTo($type)
				->string($record->getRequestId())->isEqualTo('1')
				->string($record->getContentData())->isEmpty()
			->if($record = new testedClass($type = rand(0, 255), $requestId = rand(0, 65535)))
			->then
				->string($record->getType())->isEqualTo($type)
				->string($record->getRequestId())->isEqualTo($requestId)
				->string($record->getContentData())->isEmpty()
			->if($record = new testedClass($type = rand(0, 255), $requestId = rand(0, 65535), $contentData = uniqid()))
			->then
				->string($record->getType())->isEqualTo($type)
				->string($record->getRequestId())->isEqualTo($requestId)
				->string($record->getContentData())->isEmpty()
				->exception(function() { new testedClass(256, rand(0, 65535), uniqid()); })
					->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
					->hasMessage('Type must be less than or equal to 255')
				->exception(function() { new testedClass(rand(0, 255), 65536, uniqid()); })
					->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
					->hasMessage('Request ID must be less than or equal to 65535')
		;
	}

	public function testCount()
	{
		$this
			->if($record = new testedClass(rand(0, 255)))
			->then
				->sizeOf($record)->isZero()
			->if($record = new testedClass(rand(0, 255), rand(0, 65535), $contentData = uniqid()))
			->then
				->sizeOf($record)->isEqualTo(strlen($record->getContentData()))
		;
	}

	public function testSetRequestId()
	{
		$this
			->if($record = new testedClass($type = rand(0, 255)))
			->then
				->object($record->setRequestId($requestId = rand(2, 65535)))->isIdenticalTo($record)
				->string($record->getRequestId())->isEqualTo($requestId)
				->exception(function() use ($record) { $record->setRequestId(65536); })
					->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
					->hasMessage('Request ID must be less than or equal to 65535')
		;
	}

	public function testGetStream()
	{
		$this
			->if($record = new testedClass($type = rand(0, 255)))
				->string($record->getStreamData())->isEqualTo("\001" . chr($type) . "\000\001\000\000\000\000")
			->if($record->getMockController()->getContentData = $contentData = uniqid())
			->then
				->string($record->getStreamData())->isEqualTo("\001" . chr($type) . "\000\001\000" . chr(strlen($contentData)) . "\000\000" . $contentData)
			->if($record->getMockController()->getContentData = str_repeat('0', 65536))
			->then
				->exception(function() use ($record) { $record->getStreamData(); })
					->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
					->hasMessage('Content data length must be less than or equal to ' . testedClass::maxContentDataLength)
		;
	}
}
