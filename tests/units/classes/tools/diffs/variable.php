<?php

namespace mageekguy\atoum\tests\units\tools\diffs;

use
	mageekguy\atoum,
	mageekguy\atoum\tools
;

require_once __DIR__ . '/../../../runner.php';

class variable extends atoum\test
{
	public function test__construct()
	{
		$diff = new tools\diffs\variable();

		$this->assert
			->variable($diff->getExpected())->isNull()
			->variable($diff->getActual())->isNull()
		;
	}

	public function testSetExpected()
	{
		$diff = new tools\diffs\variable();

		$this->assert
			->object($diff->setExpected($variable = uniqid()))->isIdenticalTo($diff)
			->string($diff->getExpected())->isEqualTo(self::dumpAsString($variable))
		;
	}

	public function testSetActual()
	{
		$diff = new tools\diffs\variable();

		$this->assert
			->object($diff->setActual($variable = uniqid()))->isIdenticalTo($diff)
			->string($diff->getActual())->isEqualTo(self::dumpAsString($variable))
		;
	}

	public function testMake()
	{
		$diff = new tools\diffs\variable();

		$exception = null;

		try
		{
			$diff->make();
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Expected is undefined')
		;

		$diff->setExpected($reference = uniqid());

		try
		{
			$diff->make();
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Actual is undefined')
		;

		$diff->setActual($reference);

		$this->assert
			->array($diff->make())->isEqualTo(array(self::dumpAsString($reference)))
		;
	}

	protected static function dumpAsString($mixed)
	{
		ob_start();

		var_dump($mixed);

		return trim(ob_get_clean());
	}
}
