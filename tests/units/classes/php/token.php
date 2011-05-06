<?php

namespace mageekguy\atoum\tests\units\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php
;

require_once(__DIR__ . '/../../runner.php');

class token extends atoum\test
{
	public function test__construct()
	{
		$token = new php\token($tag = uniqid(), $value = uniqid(), $line = rand(1, PHP_INT_MAX));

		$this->assert
			->string($token->getTag())->isEqualTo($tag)
			->string($token->getValue())->isEqualTo($value)
			->integer($token->getLine())->isEqualTo($line)
		;
	}
}

?>
