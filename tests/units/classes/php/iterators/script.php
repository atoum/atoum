<?php

namespace mageekguy\atoum\tests\units\php\iterators;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php,
	\mageekguy\atoum\php\iterators
;

require_once(__DIR__ . '/../../../runner.php');

class script extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->isSubclassOf('\mageekguy\atoum\php\iterator')
		;
	}

	public function test__construct()
	{
		$script = new iterators\script('');

		$this->assert
			->sizeof($script)->isZero()
		;

		$script = new iterators\script($value = uniqid());

		$this->assert
			->sizeof($script)->isEqualTo(1)
			->object($script->current())->isEqualTo(new php\token(311, $value, 1))
		;
	}

	public function testParseString()
	{
		$script = new iterators\script();

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
			->object($script->current())->isEqualTo(new php\token(368, '<?php ', 1))
//			->object($script->next()->current())->isInstanceOf('\mageekguy\atoum\php\iterators\namespace')
		;
	}
}

?>
