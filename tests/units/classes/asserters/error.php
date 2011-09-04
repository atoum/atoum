<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters
;

require_once __DIR__ . '/../../runner.php';

class error extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserter')
		;
	}

	public function testGetAsString()
	{
		$this->assert
			->string(asserters\error::getAsString(E_ERROR))->isEqualTo('E_ERROR')
			->string(asserters\error::getAsString(E_WARNING))->isEqualTo('E_WARNING')
			->string(asserters\error::getAsString(E_PARSE))->isEqualTo('E_PARSE')
			->string(asserters\error::getAsString(E_NOTICE))->isEqualTo('E_NOTICE')
			->string(asserters\error::getAsString(E_CORE_ERROR))->isEqualTo('E_CORE_ERROR')
			->string(asserters\error::getAsString(E_CORE_WARNING))->isEqualTo('E_CORE_WARNING')
			->string(asserters\error::getAsString(E_COMPILE_ERROR))->isEqualTo('E_COMPILE_ERROR')
			->string(asserters\error::getAsString(E_COMPILE_WARNING))->isEqualTo('E_COMPILE_WARNING')
			->string(asserters\error::getAsString(E_USER_ERROR))->isEqualTo('E_USER_ERROR')
			->string(asserters\error::getAsString(E_USER_WARNING))->isEqualTo('E_USER_WARNING')
			->string(asserters\error::getAsString(E_USER_NOTICE))->isEqualTo('E_USER_NOTICE')
			->string(asserters\error::getAsString(E_STRICT))->isEqualTo('E_STRICT')
			->string(asserters\error::getAsString(E_RECOVERABLE_ERROR))->isEqualTo('E_RECOVERABLE_ERROR')
			->string(asserters\error::getAsString(E_DEPRECATED))->isEqualTo('E_DEPRECATED')
			->string(asserters\error::getAsString(E_USER_DEPRECATED))->isEqualTo('E_USER_DEPRECATED')
			->string(asserters\error::getAsString(E_ALL))->isEqualTo('E_ALL')
			->string(asserters\error::getAsString(uniqid()))->isEqualTo('UNKNOWN')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\error($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getMessage())->isNull()
			->variable($asserter->getType())->isNull()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\error(new asserter\generator(new self($score = new atoum\score())));

		$this->assert
			->object($asserter->setWith(null, null))->isIdenticalTo($asserter)
			->variable($asserter->getMessage())->isNull()
			->variable($asserter->getType())->isNull()
		;

		$this->assert
			->object($asserter->setWith($message = uniqid(), null))->isIdenticalTo($asserter)
			->string($asserter->getMessage())->isEqualTo($message)
			->variable($asserter->getType())->isNull()
		;

		$this->assert
			->object($asserter->setWith($message = uniqid(), $type = rand(0, PHP_INT_MAX)))->isIdenticalTo($asserter)
			->string($asserter->getMessage())->isEqualTo($message)
			->integer($asserter->getType())->isEqualTo($type)
		;
	}

	public function testExists()
	{
		$asserter = new asserters\error(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
				->hasMessage($test->getLocale()->_('error does not exist'))
				->isInstanceOf('mageekguy\atoum\asserter\exception')
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => $test->getLocale()->_('error does not exist')
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(0, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->object($asserter->exists())->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => $test->getLocale()->_('error does not exist')
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
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('error with message \'%s\' does not exist'), $message))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => sprintf($test->getLocale()->_('error with message \'%s\' does not exist'), $message)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(0, PHP_INT_MAX), $message, uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->object($asserter->exists())->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => sprintf($test->getLocale()->_('error with message \'%s\' does not exist'), $message)
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(1)
			->array($score->getErrors())->isEmpty()
		;

		$score->reset();

		$asserter->setWith($message = uniqid(), $type = E_USER_ERROR);

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('error of type %s with message \'%s\' does not exist'), asserters\error::getAsString($type), $message))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => sprintf($test->getLocale()->_('error of type %s with message \'%s\' does not exist'), asserters\error::getAsString($type), $message)
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $message, uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->object($asserter->exists())->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => sprintf($test->getLocale()->_('error of type %s with message \'%s\' does not exist'), asserters\error::getAsString($type), $message)
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(1)
			->array($score->getErrors())->isEmpty()
		;

		$score->reset();

		$asserter->setWith(null, $type = E_USER_ERROR);

		$this->assert
			->exception(function() use (& $line, $asserter) { $line = __LINE__; $asserter->exists(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('error of type %s does not exist'), asserters\error::getAsString($type)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => sprintf($test->getLocale()->_('error of type %s does not exist'), asserters\error::getAsString($type))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->object($asserter->exists())->isIdenticalTo($asserter)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::exists()',
						'fail' => sprintf($test->getLocale()->_('error of type %s does not exist'), asserters\error::getAsString($type))
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(1)
			->array($score->getErrors())->isEmpty()
		;
	}
}

?>
