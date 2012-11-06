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
		$this
			->if($diff = new tools\diffs\variable())
			->then
				->variable($diff->getReference())->isNull()
				->variable($diff->getData())->isNull()
		;
	}

	public function testSetReference()
	{
		$this
			->if($diff = new tools\diffs\variable())
			->then
				->object($diff->setReference($variable = uniqid()))->isIdenticalTo($diff)
				->string($diff->getReference())->isEqualTo(self::dumpAsString($variable))
		;
	}

	public function testSetData()
	{
		$this
			->if($diff = new tools\diffs\variable())
			->then
				->object($diff->setData($variable = uniqid()))->isIdenticalTo($diff)
				->string($diff->getData())->isEqualTo(self::dumpAsString($variable))
		;
	}

	public function testMake()
	{
		$this
			->if($diff = new tools\diffs\variable())
			->and($exception = null)
			->then
				->exception(function () use($diff) {$diff->make();})
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Reference is undefined')
			->if($diff->setReference($reference = uniqid()))
			->then
				->exception(function () use($diff) {$diff->make();})
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Data is undefined')
			->if($diff->setData($reference))
			->then
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
