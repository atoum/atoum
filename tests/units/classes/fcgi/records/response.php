<?php

namespace mageekguy\atoum\tests\units\fcgi\records;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi,
	mock\mageekguy\atoum\fcgi\records\response as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class response extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->isSubclassOf('mageekguy\atoum\fcgi\record')
			->isAbstract()
		;
	}

	public function test__construct()
	{
		$this
			->if($record = new testedClass($type = rand(0, 255), $requestId = rand(0, 65535)))
			->then
				->string($record->getType())->isEqualTo($type)
				->string($record->getRequestId())->isEqualTo($requestId)
				->string($record->getContentData())->isEmpty()
			->if($record = new testedClass($type = rand(0, 255), $requestId = rand(0, 65535)))
			->then
				->string($record->getType())->isEqualTo($type)
				->string($record->getRequestId())->isEqualTo($requestId)
				->string($record->getContentData())->isEmpty()
			->exception(function() { new testedClass(256, rand(0, 65535)); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Type must be less than or equal to 255')
			->exception(function() { new testedClass(rand(0, 255), 65536); })
				->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
				->hasMessage('Request ID must be less than or equal to 65535')
		;
	}

	public function testAddToResponse()
	{
		$this
			->if($response = new testedClass(rand(1, 255), rand(1, 65535)))
			->then
				->object($response->addToResponse(new fcgi\response(new fcgi\request())))->isIdenticalTo($response)
		;
	}
}
