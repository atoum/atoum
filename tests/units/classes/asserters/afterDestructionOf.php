<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class classWithDestructor
{
	public function __destruct() {}
}

class classWithoutDestructor {}

class afterDestructionOf extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($asserter = new asserters\afterDestructionOf($generator = new asserter\generator($this)))
			->then
				->object($asserter->getScore())->isIdenticalTo($this->getScore())
				->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
		;
	}

	public function testSetWith()
	{
		$this
			->mock('mageekguy\atoum\tests\units\asserters\classWithDestructor')
			->assert
				->if($asserter = new asserters\afterDestructionOf(new asserter\generator($test = new self($score = new atoum\score()))))
				->and($value = uniqid())
				->then
					->exception(function() use (& $line, $asserter, $value) { $line = __LINE__; $asserter->setWith($value); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($test->getLocale()->_('%s is not an object'), $asserter->getTypeOf($value)))
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
								'fail' => sprintf($test->getLocale()->_('%s is not an object'), $asserter->getTypeOf($value))
							)
						)
					)
					->integer($score->getPassNumber())->isZero()
					->object($asserter->setWith($objectWithDestructor = new \mock\mageekguy\atoum\tests\units\asserters\classWithDestructor()))->isIdenticalTo($asserter)
					->mock($objectWithDestructor)
						->call('__destruct')->once()
					->integer($score->getPassNumber())->isEqualTo(1)
				->if($objectWithoutDestructor = new classWithoutDestructor())
				->then
					->exception(function() use (& $otherLine, $asserter, $objectWithoutDestructor) { $otherLine = __LINE__; $asserter->setWith($objectWithoutDestructor); })
						->isInstanceOf('mageekguy\atoum\asserter\exception')
						->hasMessage(sprintf($test->getLocale()->_('Destructor of class %s is undefined'), get_class($objectWithoutDestructor)))
					->integer($score->getFailNumber())->isEqualTo(2)
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
								'fail' => sprintf($test->getLocale()->_('%s is not an object'), $asserter->getTypeOf($value))
							),
							array(
								'case' => null,
								'dataSetKey' => null,
								'dataSetProvider' => null,
								'class' => __CLASS__,
								'method' => $test->getCurrentMethod(),
								'file' => __FILE__,
								'line' => $otherLine,
								'asserter' => get_class($asserter) . '::setWith()',
								'fail' => sprintf($test->getLocale()->_('Destructor of class %s is undefined'), get_class($objectWithoutDestructor))
							)
						)
					)
					->integer($score->getPassNumber())->isEqualTo(1)
		;
	}
}

?>
