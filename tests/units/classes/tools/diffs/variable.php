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
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getExpected())->isNull()
				->variable($this->testedInstance->getActual())->isNull()
				->object($this->testedInstance->getDecorator())->isEqualTo(new tools\diff\decorator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new tools\variable\analyzer())

			->if($this->newTestedInstance($reference = uniqid()))
			->then
				->string($this->testedInstance->getExpected())->isEqualTo($this->testedInstance->getAnalyzer()->dump($reference))
				->variable($this->testedInstance->getActual())->isNull()
				->object($this->testedInstance->getDecorator())->isEqualTo(new tools\diff\decorator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new tools\variable\analyzer())

			->if($this->newTestedInstance($reference, $actual = uniqid()))
			->then
				->string($this->testedInstance->getExpected())->isEqualTo($this->testedInstance->getAnalyzer()->dump($reference))
				->string($this->testedInstance->getActual())->isEqualTo($this->testedInstance->getAnalyzer()->dump($actual))
				->object($this->testedInstance->getDecorator())->isEqualTo(new tools\diff\decorator())
				->object($this->testedInstance->getAnalyzer())->isEqualTo(new tools\variable\analyzer())
		;
	}

	public function testSetAnalyzer()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setAnalyzer($analyzer = new tools\variable\analyzer()))->isTestedInstance
				->object($this->testedInstance->getAnalyzer())->isIdenticalTo($analyzer)
				->object($this->testedInstance->setAnalyzer())->isTestedInstance
				->object($this->testedInstance->getAnalyzer())
					->isNotIdenticalTo($analyzer)
					->isEqualTo(new tools\variable\analyzer())
		;
	}

	public function testSetExpected()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setExpected($variable = uniqid()))->isTestedInstance
				->string($this->testedInstance->getExpected())->isEqualTo(self::dumpAsString($variable))
		;
	}

	public function testSetActual()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setActual($variable = uniqid()))->isTestedInstance
				->string($this->testedInstance->getActual())->isEqualTo(self::dumpAsString($variable))
		;
	}

	public function testMake()
	{
		$this
			->if($diff = $this->newTestedInstance)
			->then
				->exception(function() use ($diff) {
						$diff->make();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Expected is undefined')
			->if($diff->setExpected($reference = uniqid()))
			->then
				->exception(function() use ($diff) {
						$diff->make();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Actual is undefined')
			->if($diff->setActual($reference))
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
