<?php

namespace mageekguy\atoum\tests\units\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\phpunit,
	mageekguy\atoum\test\phpunit\asserters\assertArrayHasKey as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class assertArrayHasKey extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->isSubClassOf('mageekguy\atoum\test\phpunit\asserters\assertArray')
		;
	}

	public function testSetWithArguments()
	{
		$this
			->if($asserter = new testedClass($generator = new atoum\asserter\generator()))
			->then
				->exception(function() use ($asserter) {
						$asserter->setWithArguments(array());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument #1 of assertArrayHasKey was not set')
			->if($expected = rand(1, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter, $expected) {
						$asserter->setWithArguments(array($expected));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument #2 of assertArrayHasKey was not set')
			->if($actual = new \stdClass())
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($asserter->getLocale()->_('%s is not an array'), $asserter->getAnalyzer()->getTypeOf($actual)))
			->if($actual = array())
			->then
				->exception(function() use ($asserter, & $actual, & $expected) {
						$asserter->setWithArguments(array($expected = rand(11, PHP_INT_MAX), $actual = range(0, 10)));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($asserter->getLocale()->_('%s has no key %s'), $asserter->getAnalyzer()->getTypeOf($actual), $asserter->getAnalyzer()->getTypeOf($expected)))
				->object($asserter->setWithArguments(array($expected, array($expected => uniqid()))))->isIdenticalTo($asserter)
		;
	}
}
