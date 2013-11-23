<?php

namespace mageekguy\atoum\tests\units\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\phpunit,
	mageekguy\atoum\test\phpunit\asserters\assertCount as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class assertCount extends atoum\test
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
					->hasMessage('Argument #1 of assertCount was not set')
			->if($expected = rand(1, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter, $expected) {
						$asserter->setWithArguments(array($expected));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument #2 of assertCount was not set')
			->if($actual = (bool) rand(0, 1))
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage(sprintf('Cannot check size of %s', gettype($actual)))
			->if($actual = new \stdClass())
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s has size %s, expected size %d', $asserter->getAnalyzer()->getTypeOf($actual), sizeof($actual), $expected))
			->if($actual = array())
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s has size 0, expected size %d', $asserter->getAnalyzer()->getTypeOf($actual), $expected))
				->object($asserter->setWithArguments(array(sizeof($actual), $actual)))->isIdenticalTo($asserter)
			->if($actual = new \mock\countable())
			->and($this->calling($actual)->count = 0)
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s has size %d, expected size %d', $asserter->getAnalyzer()->getTypeOf($actual), sizeof($actual), $expected))
				->object($asserter->setWithArguments(array(sizeof($actual), $actual)))->isIdenticalTo($asserter)
			->if($actual = new \mock\iterator())
			->and($this->calling($actual)->valid[1] = true)
			->and($this->calling($actual)->valid[2] = true)
			->and($this->calling($actual)->valid = false)
			->and($expected = rand(3, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter, $actual, $expected) {
						$asserter->setWithArguments(array($expected, $actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s has size %d, expected size %d', $asserter->getAnalyzer()->getTypeOf($actual), 2, $expected))
			->if($actual = new \mock\iterator())
			->and($this->calling($actual)->valid[1] = true)
			->and($this->calling($actual)->valid[2] = true)
			->and($this->calling($actual)->valid = false)
			->then
				->object($asserter->setWithArguments(array(2, $actual)))->isIdenticalTo($asserter)
		;
	}
}
