<?php

namespace mageekguy\atoum\tests\units\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\asserter;
use \mageekguy\atoum\asserters;
use \mageekguy\atoum\tools\diffs;

require_once(__DIR__ . '/../../runner.php');

class string extends atoum\test
{
	public function test__construct()
	{
		$asserter = new asserters\string($score = new atoum\score(), $locale = new atoum\locale(), $generator = new asserter\generator($this));

		$this->assert
			->object($asserter)->isInstanceOf('\mageekguy\atoum\asserters\variable')
			->object($asserter->getScore())->isIdenticalTo($score)
			->object($asserter->getLocale())->isIdenticalTo($locale)
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->variable($asserter->getVariable())->isNull()
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$currentMethod = substr(__METHOD__, strrpos(__METHOD__, ':') + 1);

		$asserter = new asserters\string($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

		$this->assert
			->exception(function() use (& $line, $asserter, & $variable) { $line = __LINE__; $asserter->setWith($variable = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($locale->_('%s is not a string'), $asserter->toString($variable)))
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $currentMethod,
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($locale->_('%s is not a string'), $asserter->toString($variable))
					)
				)
			)
			->integer($score->getPassNumber())->isZero()
			->integer($asserter->getVariable())->isEqualTo($variable)
			->variable($asserter->getCharlist())->isNull()
		;

		$this->assert
			->object($asserter->setWith($variable = uniqid()))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getPassNumber())->isEqualTo(1)
			->string($asserter->getVariable())->isEqualTo($variable)
			->variable($asserter->getCharlist())->isNull()
		;

		$score->reset();

		$this->assert
			->object($asserter->setWith($variable = uniqid(), null, $charlist = "\010"))->isIdenticalTo($asserter)
		;

		$this->assert
			->integer($score->getFailNumber())->isZero()
			->integer($score->getPassNumber())->isEqualTo(1)
			->string($asserter->getVariable())->isEqualTo($variable)
			->string($asserter->getCharlist())->isEqualTo($charlist)
		;
	}

	public function testToString()
	{
		$asserter = new asserters\string(new atoum\score(), new atoum\locale(), new asserter\generator($this));

		$asserter->setWith($variable = uniqid());

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($variable) . ') \'' . $variable . '\'')
		;

		$asserter->setWith($variable = "\010" . uniqid() . "\010", null, $charlist = "\010");

		$this->assert
			->string((string) $asserter)->isEqualTo('string(' . strlen($variable) . ') \'' . addcslashes($variable, "\010") . '\'')
		;
	}

	public function testIsEqualTo()
	{
		$asserter = new asserters\string($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isEqualTo(uniqid());
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Variable is undefined')
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
				->hasMessage($locale->_('strings are not equals') . PHP_EOL . $diff->setReference($secondString)->setData($firstString))
		;
	}

	public function testIsEmpty()
	{
		$asserter = new asserters\string($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isEmpty();
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Variable is undefined')
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
				->hasMessage($locale->_('strings are not equals') . PHP_EOL . $diff->setReference('')->setData($string))
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
		$asserter = new asserters\string($score = new atoum\score(), $locale = new atoum\locale(), new asserter\generator($this));

		$this->assert
			->boolean($asserter->wasSet())->isFalse()
			->exception(function() use ($asserter) {
						$asserter->isNotEmpty();
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Variable is undefined')
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
				->hasMessage($locale->_('string is empty'))
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
