<?php

namespace mageekguy\atoum\tests\units\fcgi;

use
	mageekguy\atoum,
	mock\mageekguy\atoum\fcgi\record as testedClass
;

require_once __DIR__ . '/../../runner.php';

class record extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isAbstract();
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::version)->isEqualTo(1)
			->integer(testedClass::maxType)->isEqualTo(255)
			->integer(testedClass::maxRequestId)->isEqualTo(65535)
			->integer(testedClass::maxContentDataLength)->isEqualTo(65535)
		;
	}

	public function test__construct()
	{
		$this
			->if($record = new testedClass($type = rand(0, 255), $requestId = rand(0, 65535)))
			->then
				->string($record->getType())->isEqualTo($type)
				->string($record->getRequestId())->isEqualTo($requestId)
				->exception(function() { new testedClass(256, rand(0, 65535)); })
					->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
					->hasMessage('Type must be less than or equal to 255')
				->exception(function() { new testedClass(rand(0, 255), 65536); })
					->isInstanceOf('mageekguy\atoum\fcgi\record\exception')
					->hasMessage('Request ID must be less than or equal to 65535')
		;
	}
}
