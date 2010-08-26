<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../../runners/autorunner.php');

class variable extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score($this);
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, $locale);

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\variable(new atoum\score($this), new atoum\locale());

		$variable = uniqid();

		$this->assert
			->object($asserter->setWith($variable))->isIdenticalTo($asserter)
			->string($asserter->getVariable())->isEqualTo($variable)
			->object($asserter->setWith($this))->isIdenticalTo($asserter)
			->object($asserter->getVariable())->isIdenticalTo($this)
		;
	}

	public function testIsEqualTo()
	{
		$score = new atoum\score($this);
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, new atoum\locale());

		$variable = uniqid();

		$asserter->setWith($variable);

		$exception = null;

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		try
		{
			$line = __LINE__; $this->assert->object($asserter->isEqualTo($variable))->isIdenticalTo($asserter);
		}
		catch (\exception $exception) {}

		$this->assert
			->variable($exception)->isNull()
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
			->collection($score->getPassAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => null
					)
				)
			)
		;

		$notEqualVariable = uniqid();

		$exception = null;

		try
		{
			$line = __LINE__; $asserter->isEqualTo($notEqualVariable);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Value \'%s\' is not equal to value \'%s\''), $variable, $notEqualVariable))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
//			->collection($score->getFailAssertions())->isEqualTo(array(
//					array(
//						'class' => __CLASS__,
//						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
//						'file' => __FILE__,
//						'line' => $line,
//						'asserter' => get_class($asserter) . '::isEqualTo()',
//						'fail' => $exception->getMessage()
//					)
//				)
//			)
		;

		var_dump($score->getFailAssertions() == array(array(
						'class' => __CLASS__,
						'method' => substr(__METHOD__, strrpos(__METHOD__, ':') + 1),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::isEqualTo()',
						'fail' => $exception->getMessage()
					))
		);

		var_dump($score->getFailAssertions());

		$asserter->setWith(1);

		$exception = null;

		try
		{
		$this->assert->object($asserter->isEqualTo('1'))->isIdenticalTo($asserter);
		}
		catch (\exception $exception) {}

		$this->assert
			->variable($exception)->isNull()
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$failMessage = uniqid();

		$exception = null;

		try
		{
			$asserter->isEqualTo($notEqualVariable, $failMessage);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsNotEqualTo()
	{
		$score = new atoum\score($this);
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, $locale);

		$variable = uniqid();

		$asserter->setWith($variable);

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$exception = null;

		try
		{
			$this->assert->object($asserter->isNotEqualTo(uniqid()))->isIdenticalTo($asserter);
		}
		catch (\exception $exception) {}

		$this->assert
			->variable($exception)->isNull()
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$exception = null;

		try
		{
			$asserter->isNotEqualTo($variable);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Value \'%s\' is equal to value \'%s\''), $variable, $variable))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$failMessage = uniqid();

		$exception = null;

		try
		{
			$asserter->isNotEqualTo($variable, $failMessage);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsIdenticalTo()
	{
		$score = new atoum\score($this);
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, $locale);

		$variable = rand(- PHP_INT_MAX, PHP_INT_MAX);

		$asserter->setWith($variable);

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$exception = null;

		try
		{
			$this->assert->object($asserter->isIdenticalTo($variable))->isIdenticalTo($asserter);
		}
		catch (\exception $exception) {}

		$this->assert
			->variable($exception)->isNull()
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$notIdenticalVariable = (string) $variable;

		$exception = null;

		try
		{
			$asserter->isIdenticalTo($notIdenticalVariable);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Value \'%s\' is not identical to value \'%s\''), $variable, $notIdenticalVariable))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$failMessage = uniqid();

		$exception = null;

		try
		{
			$asserter->isIdenticalTo($notIdenticalVariable, $failMessage);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($failMessage)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testIsNull()
	{
		$score = new atoum\score($this);
		$locale = new atoum\locale();

		$asserter = new asserters\variable($score, $locale);

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith(null);

		$exception = null;

		try
		{
			$this->assert->object($asserter->isNull())->isIdenticalTo($asserter);
		}
		catch (\exception $exception) {}

		$this->assert
			->variable($exception)->isNull()
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$variable = '';

		$asserter->setWith($variable);

		$exception = null;

		try
		{
			$asserter->isNull();
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Value \'%s\' is not null'), $variable))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$variable = uniqid();

		$asserter->setWith($variable);

		$exception = null;

		try
		{
			$asserter->isNull();
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Value \'%s\' is not null'), $variable))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;

		$variable = 0;

		$asserter->setWith($variable);

		$exception = null;

		try
		{
			$asserter->isNull();
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Value \'%s\' is not null'), $variable))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(3)
		;

		$variable = false;

		$asserter->setWith($variable);

		$exception = null;

		try
		{
			$asserter->isNull();
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('Value \'%s\' is not null'), $variable))
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(4)
		;
	}
}

?>
