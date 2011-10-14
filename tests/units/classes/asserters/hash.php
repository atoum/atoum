<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class hash extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\asserters\string')
		;
	}

	public function testIsSha1()
	{
		$asserter = new asserters\hash(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = hash('sha1', 'hello'));

		$score->reset();

		$this->assert
			->object($asserter->isSha1())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith($newvalue = substr($value, 1));

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference( $newvalue )->setData($value);

		$this->assert
			->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha1(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isSha1()',
					'fail' => sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value))
					)
				)
			)
		;

		$asserter->setWith($newvalue = 'z'.substr($value, 1) );

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference($newvalue)->setData($value);

		$this->assert
			->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha1(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s does not match given pattern'), $asserter))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isSha1()',
					'fail' => sprintf($test->getLocale()->_('%s does not match given pattern'), $asserter)
					)
				)
			)
		;
	}

	public function testIsSha256()
	{
		$asserter = new asserters\hash(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = hash('sha256', 'hello'));

		$score->reset();

		$this->assert
			->object($asserter->isSha256())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith($newvalue = substr($value, 1));

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference( $newvalue )->setData($value);

		$this->assert
			->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha256(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isSha256()',
					'fail' => sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value))
					)
				)
			)
		;

		$asserter->setWith($newvalue = 'z'.substr($value, 1) );

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference($newvalue)->setData($value);

		$this->assert
			->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha256(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s does not match given pattern'), $asserter))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isSha256()',
					'fail' => sprintf($test->getLocale()->_('%s does not match given pattern'), $asserter)
					)
				)
			)
		;
	}

	public function testIsSha512()
	{
		$asserter = new asserters\hash(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = hash('sha512', 'hello'));

		$score->reset();

		$this->assert
			->object($asserter->isSha512())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith($newvalue = substr($value, 1));

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference( $newvalue )->setData($value);

		$this->assert
			->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha512(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isSha512()',
					'fail' => sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value))
					)
				)
			)
		;

		$asserter->setWith($newvalue = 'z'.substr($value, 1) );

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference($newvalue)->setData($value);

		$this->assert
			->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha512(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s does not match given pattern'), $asserter))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isSha512()',
					'fail' => sprintf($test->getLocale()->_('%s does not match given pattern'), $asserter)
					)
				)
			)
		;
	}

	public function testIsMd5()
	{
		$asserter = new asserters\hash(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = hash('md5', 'hello'));

		$score->reset();

		$this->assert
			->object($asserter->isMd5())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;

		$asserter->setWith($newvalue = substr($value, 1));

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference( $newvalue )->setData($value);

		$this->assert
			->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isMd5(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value)))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isMd5()',
					'fail' => sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value))
					)
				)
			)
		;

		$asserter->setWith($newvalue = 'z'.substr($value, 1) );

		$score->reset();

		$diff = new diffs\variable();

		$diff->setReference($newvalue)->setData($value);

		$this->assert
			->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isMd5(); })
				->isInstanceOf('mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s does not match given pattern'), $asserter))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
				array(
					'case' => null,
					'class' => __CLASS__,
					'method' => $test->getCurrentMethod(),
					'file' => __FILE__,
					'line' => $line,
					'asserter' => get_class($asserter) . '::isMd5()',
					'fail' => sprintf($test->getLocale()->_('%s does not match given pattern'), $asserter)
					)
				)
			)
		;
	}
}

?>
