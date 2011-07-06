<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\memory;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\tests\units,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report\fields\runner\tests\memory
;

require_once(__DIR__ . '/../../../../../../runner.php');

class cli extends units\report\fields\runner\tests\memory
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubClassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function test__construct()
	{
		$field = new memory\cli();

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
		;

		$field = new memory\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $memoryColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getMemoryColorizer())->isIdenticalTo($memoryColorizer)
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
		;
	}

	public function testSetPrompt()
	{
		$field = new memory\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new memory\cli();

		$this->mock
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
		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(1, PHP_INT_MAX); };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = $testNumber = rand(1, PHP_INT_MAX);
		$runnerController->getScore = $score;

		$field = new memory\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getTitleColorizer()->colorize($field->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $field->getMemoryColorizer()->colorize($field->getLocale()->_('unknown')) . '.' . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getTitleColorizer()->colorize($field->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $field->getMemoryColorizer()->colorize($field->getLocale()->_('unknown')) . '.' . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getTitleColorizer()->colorize($field->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $field->getMemoryColorizer()->colorize($field->getLocale()->_('unknown')) . '.' . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . $field->getTitleColorizer()->colorize($field->getLocale()->__('Total test memory usage', 'Total tests memory usage', $testNumber)) . ': ' . $field->getMemoryColorizer()->colorize(sprintf($field->getLocale()->_('%4.2f Mb'), $totalMemoryUsage / 1048576)) . '.' . PHP_EOL)
		;

		$field = new memory\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $memoryColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $titleColorizer->colorize($field->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $memoryColorizer->colorize($field->getLocale()->_('unknown')) . '.' . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $titleColorizer->colorize($field->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $memoryColorizer->colorize($field->getLocale()->_('unknown')) . '.' . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $titleColorizer->colorize($field->getLocale()->__('Total test memory usage', 'Total tests memory usage', 0)) . ': ' . $memoryColorizer->colorize($field->getLocale()->_('unknown')) . '.' . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . $titleColorizer->colorize($field->getLocale()->__('Total test memory usage', 'Total tests memory usage', $testNumber)) . ': ' . $memoryColorizer->colorize(sprintf($field->getLocale()->_('%4.2f Mb'), $totalMemoryUsage / 1048576)) . '.' . PHP_EOL)
		;
	}
}

?>
