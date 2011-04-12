<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserter;
use \mageekguy\atoum\asserters;

require_once(__DIR__ . '/../../runner.php');

class error extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\error($score = new atoum\score(), $locale = new atoum\locale(), $generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getMessage())->isNull()
			->variable($asserter->getType())->isNull()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\error($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

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

		$asserter = new asserters\error($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($locale->_('error does not exist'))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => $locale->_('error does not exist')
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(0, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->object($asserter->exists())->isIdenticalTo($asserter); $otherLine = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => $locale->_('error does not exist')
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(1)
			->array($score->getErrors())->isEmpty()
		;

		$score->reset();

		$asserter->setWith($message = uniqid(), null);

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('error with message \'%s\' does not exist'), $message))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => sprintf($locale->_('error with message \'%s\' does not exist'), $message)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(0, PHP_INT_MAX), $message, uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->object($asserter->exists())->isIdenticalTo($asserter); $otherLine = __LINE__
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => sprintf($locale->_('error with message \'%s\' does not exist'), $message)
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(1)
			->array($score->getErrors())->isEmpty()
		;
	}
}

?>
