<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

class mock extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\mock($score, $locale);

		$this->assert
			->object($asserter)->isInstanceOf('\mageekguy\atoum\asserter')
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\mock($score = new atoum\score(), $locale = new atoum\locale());

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);

		$mock = uniqid();

		$this->assert
			->exception(function() use ($asserter, $mock) {
						$asserter->setWith($mock);
					}
				)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not a mock'), $mock))
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isZero()
		;

		$mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock();

		$this->assert
			->object($asserter->setWith($mock))->isIdenticalTo($asserter)
			->object($asserter->getMock())->isIdenticalTo($mock)
		;
	}

	public function testWasCalled()
	{
		$asserter = new asserters\mock($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->wasCalled();
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Mock is undefined')
		;

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);
		$mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock();

		$asserter->setWith($mock);

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->wasCalled(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not called'), get_class($mock)))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::wasCalled()',
						'fail' => sprintf($locale->_('%s is not called'), get_class($mock))
					)
				)
			)
		;

		$score->reset();

		$failMessage = uniqid();

		$this->assert
			->exception(function() use (& $line, $asserter, $failMessage) { $line = __LINE__; $asserter->wasCalled($failMessage); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::wasCalled()',
						'fail' => $failMessage
					)
				)
			)
		;

		$mock->getMockController()->{__FUNCTION__} = function() {};
		$mock->{__FUNCTION__}();

		$this->assert
			->object($asserter->wasCalled())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;
	}

	public function testCallMethod()
	{
		$asserter = new asserters\mock($score = new atoum\score(), $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($asserter) {
						$asserter->callMethod(uniqid());
					}
				)
					->isInstanceOf('\logicException')
					->hasMessage('Mock is undefined')
		;

		$mockGenerator = new atoum\mock\generator();
		$mockGenerator->generate(__CLASS__);

		$mock = new atoum\mock\mageekguy\atoum\tests\units\asserters\mock();
		$mock->getMockController()->{__FUNCTION__} = function() {};

		$asserter->setWith($mock);

		$score->reset();

		$method = __FUNCTION__;

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter, $method) { $line = __LINE__; $asserter->callMethod($method); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Method %s is not called'), $method))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::callMethod()',
						'fail' => sprintf($locale->_('Method %s is not called'), $method)
					)
				)
			)
		;

	}
}

?>
