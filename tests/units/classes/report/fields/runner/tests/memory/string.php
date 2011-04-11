<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\memory;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner\tests
;

require_once(__DIR__ . '/../../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\tests\memory
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubClassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(tests\memory\string::defaultPrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$field = new tests\memory\string();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getPrompt())->isEqualTo(tests\memory\string::defaultPrompt)
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
		;

		$field = new tests\memory\string($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getPrompt())->isEqualTo($prompt)
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
		;
	}

	public function testSetPrompt()
	{
		$field = new tests\memory\string();

		$this->assert
			->object($field->setPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo($prompt)
			->object($field->setPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new tests\memory\string($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(1, PHP_INT_MAX); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = function () use ($score) { return $score; };
		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = rand(0, PHP_INT_MAX); };

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
			->integer($field->getValue())->isEqualTo($totalMemoryUsage)
			->integer($field->getTestNumber())->isEqualTo($testNumber)
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
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(1, PHP_INT_MAX); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = 1; };
		$runnerController->getScore = function () use ($score) { return $score; };

		$field = new tests\memory\string();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test memory usage: %4.2f Mb.', 'Total test memory usage: %4.2f Mb.', $totalMemoryUsage / 1048576), $totalMemoryUsage / 1048576) . PHP_EOL)
		;

		$field = new tests\memory\string($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test memory usage: %4.2f Mb.', 'Total test memory usage: %4.2f Mb.', $totalMemoryUsage / 1048576), $totalMemoryUsage / 1048576) . PHP_EOL)
		;

		$runnerController->getTestNumber = function () use (& $testNumber) { return $testNumber = rand(2, PHP_INT_MAX); };

		$field = new tests\memory\string();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test memory usage: %4.2f Mb.', 'Total tests memory usage: %4.2f Mb.', $testNumber), $totalMemoryUsage / 1048576) . PHP_EOL)
		;

		$field = new tests\memory\string($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test memory usage: %4.2f Mb.', 'Total tests memory usage: %4.2f Mb.', $testNumber), $totalMemoryUsage / 1048576) . PHP_EOL)
		;
	}
}

?>
