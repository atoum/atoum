<?php

namespace mageekguy\atoum\tests\units\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\phpunit,
	mageekguy\atoum\test\phpunit\asserters\assertInstanceOf as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class assertInstanceOf extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->isSubClassOf('\\mageekguy\\atoum\\asserter')
		;
	}

	public function testSetWithArguments()
	{
		$this
			->if($asserter = new testedClass())
			->then
				->exception(function() use ($asserter) {
						$asserter->setWithArguments(array());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument #1 of assertInstanceOf was not set')
			->if($expected = uniqid())
			->then
				->exception(function() use ($asserter, $expected) {
						$asserter->setWithArguments(array($expected));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument #2 of assertInstanceOf was not set')
			->if($actual = new \stdClass())
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s is not an instance of %s', $asserter->getAnalyzer()->getTypeOf($actual), $expected))
			->if($expected = new \mock\stdClass())
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s is not an instance of %s', $asserter->getAnalyzer()->getTypeOf($actual), $asserter->getAnalyzer()->getTypeOf($expected)))
			->if($actual = new \stdClass())
			->and($expected = 'stdClass')
			->then
				->object($asserter->setWithArguments(array($expected, $actual)))->isIdenticalTo($asserter)
			->if($expected = new \stdClass())
			->then
				->object($asserter->setWithArguments(array($expected, $actual)))->isIdenticalTo($asserter)
		;
	}
}
