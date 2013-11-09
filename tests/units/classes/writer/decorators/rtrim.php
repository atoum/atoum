<?php

namespace mageekguy\atoum\tests\units\writer\decorators;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\writer\decorators\rtrim as testedClass
;

class rtrim extends atoum
{
	public function testDecorate()
	{
		$this
			->if($decorator = new testedClass())
			->then
				->string($decorator->decorate($message = uniqid()))->isEqualTo($message)
				->string($decorator->decorate(($message = uniqid()) . PHP_EOL))->isEqualTo($message)
				->string($decorator->decorate(' ' . ($message = uniqid()) . PHP_EOL . PHP_EOL))->isEqualTo(' ' . $message)
				->string($decorator->decorate(PHP_EOL . ' ' . ($message = uniqid()) . PHP_EOL . PHP_EOL))->isEqualTo(PHP_EOL . ' ' . $message)
		;
	}
}
