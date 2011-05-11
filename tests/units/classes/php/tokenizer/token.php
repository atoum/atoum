<?php

namespace mageekguy\atoum\tests\units\php\tokenizer;

use
	\mageekguy\atoum,
	\mageekguy\atoum\php\tokenizer
;

require_once(__DIR__ . '/../../../runner.php');

class token extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\php\tokenizer\iterator\value')
		;
	}

	public function test__construct()
	{
		$token = new tokenizer\token($tag = uniqid(), $string = uniqid(), $line = rand(1, PHP_INT_MAX));

		$this->assert
			->string($token->getTag())->isEqualTo($tag)
			->string($token->getValue())->isEqualTo($string)
			->integer($token->getLine())->isEqualTo($line)
		;
	}

	public function test__toString()
	{
		$token = new tokenizer\token($tag = uniqid(), $string = uniqid(), $line = rand(1, PHP_INT_MAX));

		$this->assert
			->castToString($token)->isEqualTo($string)
		;

		$token = new tokenizer\token($tag = uniqid(), null, $line = rand(1, PHP_INT_MAX));

		$this->assert
			->castToString($token)->isEqualTo($tag)
		;
	}
}

?>
