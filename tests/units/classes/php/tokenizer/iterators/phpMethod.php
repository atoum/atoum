<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

require_once __DIR__ . '/../../../../runner.php';

class phpMethod extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('mageekguy\atoum\php\tokenizer\iterators\phpFunction')
		;
	}
}
