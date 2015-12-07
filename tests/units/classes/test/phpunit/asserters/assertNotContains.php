<?php

namespace mageekguy\atoum\tests\units\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\phpunit,
	mageekguy\atoum\test\phpunit\asserters\assertNotContains as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class assertNotContains extends atoum\test
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
					->hasMessage('Argument #1 of assertNotContains was not set')
			->if($expected = rand(1, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter, $expected) {
						$asserter->setWithArguments(array($expected));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument #2 of assertNotContains was not set')
			->if($actual = (bool) rand(0, 1))
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage(sprintf('Cannot check containment in %s values', gettype($actual)))
			->if($actual = new \stdClass())
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage(sprintf('Cannot check containment in object(%s)', get_class($actual)))
			->if($actual = array($expected = uniqid()))
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s contains %s', $asserter->getAnalyzer()->getTypeOf($actual), $asserter->getAnalyzer()->getTypeOf($expected)))
				->object($asserter->setWithArguments(array(uniqid(), $actual)))->isIdenticalTo($asserter)
			->if($actual = new \recursiveArrayIterator(array($expected = uniqid(), $expectedObject = new \stdClass())))
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s contains %s', $asserter->getAnalyzer()->getTypeOf($actual), $asserter->getAnalyzer()->getTypeOf($expected)))
				->exception(function() use ($asserter, $actual, $expectedObject) {
						$asserter->setWithArguments(array($expectedObject, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s contains %s', $asserter->getAnalyzer()->getTypeOf($actual), $asserter->getAnalyzer()->getTypeOf($expectedObject)))
				->object($asserter->setWithArguments(array(new \stdClass, $actual)))->isIdenticalTo($asserter)
		;
	}
}
