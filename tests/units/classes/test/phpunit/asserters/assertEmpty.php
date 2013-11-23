<?php

namespace mageekguy\atoum\tests\units\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\mock,
	mageekguy\atoum\tools\diffs,
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
			->if($asserter = new testedClass($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) {
						$asserter->setWithArguments(array());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Argument #1 of assertEmpty was not set')
			->if($actual = rand(0, PHP_INT_MAX))
			->then
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage(sprintf('Cannot check if %s is empty', gettype($actual)))
			->if($actual = array(uniqid()))
			->then
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s is not empty', $asserter->getAnalyzer()->getTypeOf($actual)))
				->object($asserter->setWithArguments(array(array())))->isIdenticalTo($asserter)
			->if($actual = uniqid())
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual));
					}
				)
					//->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_(sprintf('string is not empty', $asserter->getAnalyzer()->getTypeOf($actual))) . PHP_EOL . $diff->setExpected('')->setActual($actual))
				->object($asserter->setWithArguments(array('')))->isIdenticalTo($asserter)
			->if($actual = new \stdClass)
			->and($actual->foo = uniqid())
			->then
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('%s has size %d', $asserter->getAnalyzer()->getTypeOf($actual), sizeof($actual)))
				->exception(function() use ($asserter, $actual) {
						$asserter->setWithArguments(array($actual = new \stdClass));
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($generator->getLocale()->_(sprintf('%s has size %d', $asserter->getAnalyzer()->getTypeOf($actual), sizeof($actual))))
		;
	}
}
