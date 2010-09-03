<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../../runners/autorunner.php');

/**
@isolation off
*/
class error extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\error($score, $locale);

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetWith()
	{
		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\error($score, $locale);

		$this->assert
			->object($asserter->setWith(null, null))->isIdenticalTo($asserter)
			->variable($asserter->getMessage())->isNull()
			->variable($asserter->getType())->isNull()
		;

		$message = uniqid();

		$this->assert
			->object($asserter->setWith($message, null))->isIdenticalTo($asserter)
			->string($asserter->getMessage())->isEqualTo($message)
			->variable($asserter->getType())->isNull()
		;

		$message = uniqid();
		$type = rand(0, PHP_INT_MAX);

		$this->assert
			->object($asserter->setWith($message, $type))->isIdenticalTo($asserter)
			->string($asserter->getMessage())->isEqualTo($message)
			->integer($asserter->getType())->isEqualTo($type)
		;
	}

	public function testExists()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$score = new atoum\score();
		$locale = new atoum\locale();

		$asserter = new asserters\error($score, $locale);

		$exception = null;

		try
		{
			$line = __LINE__; $asserter->exists();
		;
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($locale->_('error does not exist'))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => $exception->getMessage()
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(0, PHP_INT_MAX), uniqid());

		$otherException = null;

		try
		{
			$otherLine = __LINE__; $asserter->exists();
		}
		catch (\exception $otherException) {}

		$this->assert
			->variable($otherException)->isNull()
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => $exception->getMessage()
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(1)
			->collection($score->getPassAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => null
					)
				)
			)
			->collection($score->getErrors())->isEmpty()
		;

		$score->reset();

		$message = uniqid();

		$asserter->setWith($message, null);

		$exception = null;

		try
		{
			$line = __LINE__; $asserter->exists();
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('error with message \'%s\' does not exist'), $message))
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => $exception->getMessage()
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(0, PHP_INT_MAX), $message);

		$otherException = null;

		try
		{
			$otherLine = __LINE__; $asserter->exists();
		}
		catch (\exception $otherException) {}

		$this->assert
			->variable($otherException)->isNull()
			->integer($score->getFailNumber())->isEqualTo(1)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => $exception->getMessage()
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(1)
			->collection($score->getPassAssertions())->isEqualTo(array(
					array(
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => null
					)
				)
			)
			->collection($score->getErrors())->isEmpty()
		;

		$score->reset();
	}
}

?>
