<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class boolean extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserters\variable')
		;
	}

	public function test__construct()
	{

		$this->assert
			->if($asserter = new asserters\boolean($generator = new asserter\generator($this)))
			->then
				->object($asserter->getScore())->isIdenticalTo($this->getScore())
				->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
				->variable($asserter->getValue())->isNull()
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testIsTrue()
	{
		$this->assert
			->if($asserter = new asserters\boolean(new asserter\generator($test= new self($score = new atoum\score()))))
			->then
				->exception(function() use ($asserter) {
						$asserter->isTrue();
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) {
						$asserter->isTrue;
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) {
						$asserter->IsTrue;
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith(true))
			->and($score->reset())
			->then
				->object($asserter->isTrue())->isIdenticalTo($asserter)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isZero()
			->if($asserter->setWith(false))
			->and($score->reset())
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) {
							$asserter->isTrue();
						}
					)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('%s is not true'), $asserter) . PHP_EOL . $diff->setReference(true)->setData(false))
			->if($asserter->setWith(true))
			->and($score->reset())
			->then
				->object($asserter->isTrue)->isIdenticalTo($asserter)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isZero()
			->if($asserter->setWith(false))
			->and($score->reset())
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) {
							$asserter->isTrue;
						}
					)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('%s is not true'), $asserter) . PHP_EOL . $diff->setReference(true)->setData(false))
		;
	}

	public function testIsFalse()
	{
		$this->assert
			->if($asserter = new asserters\boolean(new asserter\generator($test = new self($score = new atoum\score()))))
			->then
				->exception(function() use ($asserter) {
						$asserter->isFalse();
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) {
						$asserter->isFalse;
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
				->exception(function() use ($asserter) {
						$asserter->IsFalse;
					}
				)
					->isInstanceOf('logicException')
					->hasMessage('Value is undefined')
			->if($asserter->setWith(false))
			->and($score->reset())
			->then
				->object($asserter->isFalse())->isIdenticalTo($asserter)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isZero()
			->if($asserter->setWith(true))
			->and($score->reset())
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) {
						$asserter->isFalse();
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('%s is not false'), $asserter) . PHP_EOL . $diff->setReference(false)->setData(true))
			->if($asserter->setWith(false))
			->and($score->reset())
			->then
				->object($asserter->isFalse)->isIdenticalTo($asserter)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isZero()
			->if($asserter->setWith(true))
			->and($score->reset())
			->and($diff = new diffs\variable())
			->then
				->exception(function() use ($asserter) {
						$asserter->isFalse;
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('%s is not false'), $asserter) . PHP_EOL . $diff->setReference(false)->setData(true))
		;
	}

	public function testSetWith()
	{
		$this->assert
			->if($asserter = new asserters\boolean(new asserter\generator($test = new self($score = new atoum\score()))))
			->then
				->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($test->getLocale()->_('%s is not a boolean'), $asserter->getTypeOf($value)))
				->integer($score->getFailNumber())->isEqualTo(1)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::setWith()',
							'fail' => sprintf($test->getLocale()->_('%s is not a boolean'), $asserter->getTypeOf($value))
						)
					)
				)
				->integer($score->getPassNumber())->isZero()
				->string($asserter->getValue())->isEqualTo($value)
				->object($asserter->setWith(true))->isIdenticalTo($asserter)
				->integer($score->getFailNumber())->isEqualTo(1)
				->integer($score->getPassNumber())->isEqualTo(1)
				->boolean($asserter->getValue())->isTrue()
		;
	}
}

?>
