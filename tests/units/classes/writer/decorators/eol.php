<?php

namespace mageekguy\atoum\tests\units\writer\decorators;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\writer\decorators\eol as testedClass
;

class eol extends atoum
{
	public function testClass()
	{
		$this->testedClass->implements('mageekguy\atoum\writer\decorator');
	}

	public function testDecorate()
	{
		$this
			->if($decorator = new testedClass())
			->then
				->string($decorator->decorate($message = uniqid()))->isEqualTo($message . PHP_EOL)
				->string($decorator->decorate($message = uniqid() . PHP_EOL))->isEqualTo($message . PHP_EOL)
				->string($decorator->decorate($message = ' ' . uniqid() . PHP_EOL . PHP_EOL))->isEqualTo($message . PHP_EOL)
		;
	}
}
