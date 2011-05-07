<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php\tokenizer
;

require_once(__DIR__ . '/../../../runner.php');

class scriptizer extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubclassOf('\mageekguy\atoum\php\tokenizer\iterator')
		;
	}

	public function test__construct()
	{
		$script = new tokenizer\scriptizer('');

		$this->assert
			->sizeof($script)->isZero()
		;

		$script = new tokenizer\scriptizer($value = uniqid());

		$this->assert
			->sizeof($script)->isEqualTo(1)
			->object($script->current())->isEqualTo(new tokenizer\token(311, $value, 1))
		;
	}

	public function testParseString()
	{
		$script = new tokenizer\scriptizer();

		$this->assert
			->sizeof($script)->isZero()
			->object($script->parseString(''))->isIdenticalTo($script)
			->sizeof($script)->isZero()
			->object($script->parseString(uniqid()))->isIdenticalTo($script)
			->sizeof($script)->isEqualTo(1)
		;

		$script->reset();

		$this->assert
			->sizeof($script)->isZero()
			->object($script->parseString('<?php namespace foo; ?>'))->isIdenticalTo($script)
			->sizeof($script)->isEqualTo(7)
			->object($script->current())->isEqualTo(new tokenizer\token(368, '<?php ', 1))
//			->object($script->next()->current())->isInstanceOf('\mageekguy\atoum\php\iterators\namespace')
		;
	}
}

?>
