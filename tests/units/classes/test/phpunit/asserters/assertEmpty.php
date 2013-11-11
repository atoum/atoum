<?php

namespace mageekguy\atoum\tests\units\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\phpunit,
	mageekguy\atoum\test\phpunit\asserters\assertEmpty as testedClass
;

require_once __DIR__ . '/../../../../runner.php';

class assertEmpty extends atoum\test
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
					->hasMessage('Argument 0 of assertEmpty was not set')
			->if($actual = rand(0, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage(sprintf('Cannot check if %s is empty', $asserter->getTypeOf($actual)))
			->if($actual = array(uniqid()))
			->then
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s is not empty', $asserter->getTypeOf($actual)))
				->object($asserter->setWithArguments(array(array())))->isIdenticalTo($asserter)
			->if($actual = uniqid())
			->then
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					//->hasMessage(sprintf('%s is not empty', $asserter->getTypeOf($actual)))
				->object($asserter->setWithArguments(array('')))->isIdenticalTo($asserter)
			->if($actual = new \stdClass)
			->and($actual->foo = uniqid())
			->then
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s has size %d', $asserter->getTypeOf($actual), sizeof($actual)))
				->object($asserter->setWithArguments(array(new \stdClass)))->isIdenticalTo($asserter)
		;
	}
}
