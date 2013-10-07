<?php

namespace mageekguy\atoum\tests\units\test\adapter\calls;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\adapter\call,
	mageekguy\atoum\test\adapter\calls,
	mageekguy\atoum\test\adapter\calls\decorator as testedClass

;

class decorator extends atoum\test
{
	public function testDecorate()
	{
		$this
			->if($decorator = new testedClass())
			->and($calls = new calls())
			->then
				->string($decorator->decorate($calls))->isEmpty()
			->if($calls[] = $call1 = new call(uniqid(), array()))
			->then
				->string($decorator->decorate($calls))->isEqualTo('[1] ' . $call1 . PHP_EOL)
			->if($calls[] = $call2 = clone $call1)
			->then
				->string($decorator->decorate($calls))->isEqualTo('[1] ' . $call1 . PHP_EOL . '[2] ' . $call2 . PHP_EOL)
			->if($calls[] = $call3 = clone $call1)
			->and($calls[] = $call4 = new call(uniqid(), array(uniqid(), uniqid())))
			->and($calls[] = $call5 = clone $call1)
			->and($calls[] = $call6 = clone $call1)
			->and($calls[] = $call7 = clone $call1)
			->and($calls[] = $call8 = clone $call1)
			->and($calls[] = $call9 = clone $call1)
			->and($calls[] = $call10 = clone $call1)
			->then
				->string($decorator->decorate($calls))->isEqualTo(
						'[ 1] ' . $call1 . PHP_EOL .
						'[ 2] ' . $call2 . PHP_EOL .
						'[ 3] ' . $call3 . PHP_EOL .
						'[ 4] ' . $call4 . PHP_EOL .
						'[ 5] ' . $call5 . PHP_EOL .
						'[ 6] ' . $call6 . PHP_EOL .
						'[ 7] ' . $call7 . PHP_EOL .
						'[ 8] ' . $call8 . PHP_EOL .
						'[ 9] ' . $call9 . PHP_EOL .
						'[10] ' . $call10 . PHP_EOL
				)
		;
	}
}
