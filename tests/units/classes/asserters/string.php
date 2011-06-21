<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters,
	\mageekguy\atoum\tools\diffs
;

require_once(__DIR__ . '/../../runner.php');

class string extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserters\variable')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\string($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getValue())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__toString()
	{
		$asserter = new asserters\string(new asserter\generator($test = new self($score = new atoum\score())));

		$asserter->setWith($value = uniqid());

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($value) . ') \'' . $value . '\'')
		;

		$asserter->setWith($value = "\010" . uniqid() . "\010", null, $charlist = "\010");

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($value) . ') \'' . addcslashes($value, "\010") . '\'')
		;
	}


	public function testSetWith()
	{
		$asserter = new asserters\string(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not a string'), $asserter->getTypeOf($value)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not a string'), $asserter->getTypeOf($value))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->integer($asserter->getValue())->isEqualTo($value)
			->variable($asserter->getCharlist())->isNull()
		;

		$this->assert
			->object($asserter->setWith($value = uniqid()))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->string($asserter->getValue())->isEqualTo($value)
			->variable($asserter->getCharlist())->isNull()
		;

		$score->reset();

		$this->assert
			->object($asserter->setWith($value = uniqid(), null, $charlist = "\010"))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
			->string($asserter->getValue())->isEqualTo($value)
			->string($asserter->getCharlist())->isEqualTo($charlist)
		;
	}

	public function testIsEqualTo()
	{
		$asserter = new asserters\string(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isEqualTo(uniqid());
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
		;

		$asserter->setWith($firstString = uniqid());

		$score->reset();

		$diff = new diffs\variable();

		$this->assert
			->exception(function() use ($asserter, & $secondString) {
						$asserter->isEqualTo($secondString = uniqid());
					}
				)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($test->getLocale()->_('strings are not equals') . PHP_EOL . $diff->setReference($secondString)->setData($firstString))
		;
	}

	public function testIsEmpty()
	{
		$asserter = new asserters\string(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isEmpty();
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
		;

		$asserter->setWith($string = uniqid());

		$score->reset();

		$diff = new diffs\variable();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use ($asserter) {
						$asserter->isEmpty();
					}
				)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($test->getLocale()->_('strings are not equals') . PHP_EOL . $diff->setReference('')->setData($string))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$asserter->setWith('');

		$score->reset();

		$diff = new diffs\variable();

		$this->assert
			->object($asserter->isEmpty())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;
	}

	public function testIsNotEmpty()
	{
		$asserter = new asserters\string(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isNotEmpty();
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Value is undefined')
		;

		$asserter->setWith('');

		$score->reset();

		$diff = new diffs\variable();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isZero()
			->exception(function() use ($asserter) {
						$asserter->isNotEmpty();
					}
				)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage($test->getLocale()->_('string is empty'))
			->integer($score->getPassNumber())->isZero()
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$asserter->setWith($string = uniqid());

		$score->reset();

		$diff = new diffs\variable();

		$this->assert
			->object($asserter->isNotEmpty())->isIdenticalTo($asserter)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isZero()
		;
	}
}

?>
