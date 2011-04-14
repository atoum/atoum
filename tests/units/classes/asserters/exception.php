<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class exception extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\exception($generator = new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($test->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getVariable())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$this->mock('\mageekguy\atoum\test');

		$asserter = new asserters\exception(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($this->getLocale()->_('%s is not an exception'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($this->getLocale()->_('%s is not an exception'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getVariable())->isEqualTo($variable)
		;

		$this->assert
			->object($asserter->setWith($variable = new \exception()))->isIdenticalTo($asserter); $line = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->exception($asserter->getVariable())->isIdenticalTo($variable)
		;
	}

	public function testHasCode()
	{
		$this->assert
			->exception(function() { throw new atoum\exceptions\runtime('An exception message to test!',33); })
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('An exception message to test!')
				->hasCode(33);
	}
}

?>
