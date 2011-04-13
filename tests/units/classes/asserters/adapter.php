<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters
;

require_once(__DIR__ . '/../../runner.php');

class adapter extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\adapter($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLOcale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getAdapter())->isNull()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a test adapter'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$this->assert
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not a test adapter'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->string($asserter->getAdapter())->isEqualTo($variable)
		;

		$this->assert
			->object($asserter->setWith($adapter = new atoum\test\adapter()))->isIdenticalTo($asserter);
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->object($asserter->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testCall()
	{
		$asserter = new asserters\adapter(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->exception(function() use ($asserter) {
						$asserter->call(uniqid());
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Adapter is undefined')
			->integer($score->getPassNumber())->isZero()
		;

		$adapter = new atoum\test\adapter();

		$asserter
			->setWith($adapter)
			->getScore()
				->reset()
		;

		$function = uniqid();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter, $function) { $line = __LINE__; $asserter->call($function); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s was not called'), $function))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::call()',
						'fail' => sprintf($test->getLocale()->_('function %s was not called'), $function)
					)
				)
			)
		;

		$adapter->{$function = 'md5'} = function() {};

		$adapter->{$function}();

		$this->assert
			->object($asserter->call($function))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$score->reset();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use (& $line, $asserter, $function) { $line = __LINE__; $asserter->call($function, array(uniqid())); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s was not called with this argument'), $function))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::call()',
						'fail' => sprintf($test->getLocale()->_('function %s was not called with this argument'), $function)
					)
				)
			)
			->exception(function() use (& $otherLine, $asserter, $function) { $otherLine = __LINE__; $asserter->call($function, array(uniqid(), uniqid())); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('function %s was not called with these arguments'), $function))
			->integer($score->getPassNumber())->isEqualTo(0)
			->integer($score->getFailNumber())->isEqualTo(2)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::call()',
						'fail' => sprintf($test->getLocale()->_('function %s was not called with this argument'), $function)
					),
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $otherLine,
						'asserter' => get_class($asserter) . '::call()',
						'fail' => sprintf($test->getLocale()->_('function %s was not called with these arguments'), $function)
					)
				)
			)
		;

		$adapter->{$function}($arg = uniqid());

		$this->assert
			->object($asserter->call($function, array($arg)))->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}
}

?>
