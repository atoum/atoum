<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner\tests
;

require_once(__DIR__ . '/../../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\runner\tests\duration
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubClassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function testClassConstant()
	{
		$this->assert
			->string(tests\duration\cli::defaultPrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$field = new tests\duration\cli();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getPrompt())->isEqualTo(tests\duration\cli::defaultPrompt)
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
		;

		$field = new tests\duration\cli($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getPrompt())->isEqualTo($prompt)
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
		;
	}

	public function testSetPrompt()
	{
		$field = new tests\duration\cli();

		$this->assert
			->object($field->setPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo($prompt)
			->object($field->setPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new tests\duration\cli();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $totalDuration) { return $totalDuration = rand(1, PHP_INT_MAX); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = function () use (& $testsNumber) { return $testsNumber = rand(1, PHP_INT_MAX); };
		$runnerController->getScore = function () use ($score) { return $score; };

		$this->assert
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($field)
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
			->integer($field->getValue())->isEqualTo($totalDuration)
			->integer($field->getTestNumber())->isEqualTo($testsNumber)
		;
	}

	public function test__toString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $totalDuration) { return $totalDuration = (rand(1, 100) / 1000); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = 1; };
		$runnerController->getScore = function () use ($score) { return $score; };

		$field = new tests\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$field = new tests\duration\cli($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = rand(2, PHP_INT_MAX); };

		$field = new tests\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$field = new tests\duration\cli($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$score->getMockController()->getTotalDuration = function() use (& $totalDuration) { return $totalDuration = rand(2, PHP_INT_MAX); };

		$field = new tests\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getLocale()->_($field->getPrompt() . 'Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$field = new tests\duration\cli($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($field->getLocale()->_($field->getPrompt() . 'Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;
	}
}

?>
