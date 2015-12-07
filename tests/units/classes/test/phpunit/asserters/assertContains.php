<?php

namespace mageekguy\atoum\tests\units\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\phpunit,
	mageekguy\atoum\test\phpunit\asserters\assertContains as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class assertContains extends atoum\test
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
					->hasMessage('Argument #1 of assertContains was not set')
			->if($expected = rand(1, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter, $expected) {
						$asserter->setWithArguments(array($expected));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument #2 of assertContains was not set')
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
			->if($actual = array())
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s does not contain %s', $asserter->getAnalyzer()->getTypeOf($actual), $asserter->getAnalyzer()->getTypeOf($expected)))
			->if($actual = array($expected = uniqid()))
			->then
				->object($asserter->setWithArguments(array($expected, $actual)))->isIdenticalTo($asserter)
			->if($actual = new \recursiveArrayIterator(array($expected = uniqid(), $expectedObject = new \stdClass())))
			->then
				->exception(function() use ($asserter, $actual, & $notExpected) {
						$asserter->setWithArguments(array($notExpected = uniqid(), $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s does not contain %s', $asserter->getAnalyzer()->getTypeOf($actual), $asserter->getAnalyzer()->getTypeOf($notExpected)))
				->exception(function() use ($asserter, $actual, & $notExpected) {
						$asserter->setWithArguments(array($notExpected = new \stdClass(), $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s does not contain %s', $asserter->getAnalyzer()->getTypeOf($actual), $asserter->getAnalyzer()->getTypeOf($notExpected)))
				->object($asserter->setWithArguments(array($expectedObject, $actual)))->isIdenticalTo($asserter)
		;
	}
}
