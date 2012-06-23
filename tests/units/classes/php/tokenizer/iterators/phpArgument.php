<?php

namespace mageekguy\atoum\tests\units\php\tokenizer\iterators;

use
	mageekguy\atoum,
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

require_once __DIR__ . '/../../../../runner.php';

class phpArgument extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubClassOf('mageekguy\atoum\php\tokenizer\iterator')
		;
	}

	public function test__construct()
	{
		$iterator = new iterators\phpArgument();

		$this->assert
			->variable($iterator->getDefaultValue())->isNull()
		;
	}

	public function testAppendDefaultValue()
	{
		$iterator = new iterators\phpArgument();

		$this->assert
			->object($iterator->appendDefaultValue($defaultValue = new iterators\phpDefaultValue()))->isIdenticalTo($iterator)
			->object($iterator->getDefaultValue())->isIdenticalTo($defaultValue)
		;
	}

	public function testGetDefaultValue()
	{
		$iterator = new iterators\phpArgument();

		$this->assert
			->variable($iterator->getDefaultValue())->isNull()
		;
	}
}
