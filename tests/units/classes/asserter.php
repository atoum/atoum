<?php

namespace mageekguy\atoum\tests\units;

require __DIR__ . '/../runner.php';

use
	mageekguy\atoum
;

class asserter extends atoum\test
{
	public function testSetWithTest()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserter(new atoum\asserter\generator()))
			->then
				->object($asserter->setWithTest($this))->isIdenticalTo($asserter)
		;
	}

	public function testSetWithArguments()
	{
		$this
			->if($asserter = new \mock\mageekguy\atoum\asserter(new atoum\asserter\generator()))
			->then
				->object($asserter->setWithArguments(array()))->isIdenticalTo($asserter)
				->mock($asserter)->call('setWith')->never()
				->object($asserter->setWithArguments(array($argument = uniqid())))->isIdenticalTo($asserter)
				->mock($asserter)->call('setWith')->withArguments($argument)->once()
		;
	}
}
