<?php

namespace atoum\tests\units\php\tokenizer\iterators;

use
	atoum,
	atoum\php\tokenizer,
	atoum\php\tokenizer\iterators
;

require_once __DIR__ . '/../../../../runner.php';

class phpProperty extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('atoum\php\tokenizer\iterator')
		;
	}
}
