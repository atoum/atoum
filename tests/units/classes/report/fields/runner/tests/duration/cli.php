<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\tests\units,
	\mageekguy\atoum\report\fields\runner\tests,
	\mageekguy\atoum\mock\mageekguy\atoum as mock
;

require_once(__DIR__ . '/../../../../../../runner.php');

class cli extends units\report\fields\runner\tests\duration
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubClassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function test__construct()
	{
		$field = new tests\duration\cli();

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getDurationColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
		;

		$field = new tests\duration\cli($prompt = new prompt(), $titleColorizer = new colorizer(), $durationColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getDurationColorizer())->isIdenticalTo($durationColorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getValue())->isNull()
			->variable($field->getTestNumber())->isNull()
		;
	}

	public function testSetPrompt()
	{
		$field = new tests\duration\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new tests\duration\cli();

		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\score();
		$score->getMockController()->getTotalDuration = $totalDuration = rand(1, PHP_INT_MAX);

		$runner = new mock\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = $testsNumber = rand(1, PHP_INT_MAX);
		$runnerController->getScore = $score;

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
		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\score();
		$score->getMockController()->getTotalDuration = $totalDuration = (rand(1, 100) / 1000);

		$runner = new mock\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getTestNumber = $testNumber = 1;
		$runnerController->getScore = $score;

		$field = new tests\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$field = new tests\duration\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $durationColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', $testNumber)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', $testNumber)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', $testNumber)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', $testNumber)),
						$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration))
					) .
					PHP_EOL
				)
		;

		$runnerController->getTestNumber = $testNumber = rand(2, PHP_INT_MAX);

		$field = new tests\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$field = new tests\duration\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $durationColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', 0)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', 0)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', 0)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', $testNumber)),
						$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration))
					) .
					PHP_EOL
				)
		;

		$score->getMockController()->getTotalDuration = $totalDuration = rand(2, PHP_INT_MAX);

		$field = new tests\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getLocale()->_($field->getPrompt() . 'Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Total test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Total test duration: %s.', 'Total tests duration: %s.', $testNumber), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration)) . PHP_EOL)
		;

		$field = new tests\duration\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $durationColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', 0)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', 0)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', 0)),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($prompt .
					sprintf(
						'%s: %s.',
						$titleColorizer->colorize($locale->__('Total test duration', 'Total tests duration', $testNumber)),
						$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $totalDuration), $totalDuration))
					) .
					PHP_EOL
				)
		;
	}
}

?>
