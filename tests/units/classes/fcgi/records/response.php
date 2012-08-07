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

	public function testAddToResponse()
	{
		$this
			->if($response = new testedClass(rand(1, 255), rand(1, 65535)))
			->then
				->object($response->addToResponse(new fcgi\response(new fcgi\request())))->isIdenticalTo($response)
		;
	}
}
