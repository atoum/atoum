<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\uncompleted;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mock\mageekguy\atoum as mock,
	mageekguy\atoum\report\fields\runner\tests
;

require_once __DIR__ . '/../../../../../../runner.php';

class cli extends units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('mageekguy\atoum\report\fields\runner')
		;
	}

	public function test__construct()
	{
		$field = new tests\uncompleted\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getMethodColorizer())->isEqualTo(new colorizer())
			->object($field->getOutputPrompt())->isEqualTo(new prompt())
			->object($field->getOutputColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getRunner())->isNull()
		;

		$field = new tests\uncompleted\cli(null, null, null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getMethodColorizer())->isEqualTo(new colorizer())
			->object($field->getOutputPrompt())->isEqualTo(new prompt())
			->object($field->getOutputColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getRunner())->isNull()
		;

		$field = new tests\uncompleted\cli ($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(), $locale = new atoum\locale());

		$this->assert
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
			->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
			->object($field->getOutputPrompt())->isIdenticalTo($outputPrompt)
			->object($field->getOutputColorizer())->isIdenticalTo($outputColorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getRunner())->isNull()
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new tests\uncompleted\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new tests\uncompleted\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new tests\uncompleted\cli();

		$this->assert
			->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getMethodPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetMethodColorizer()
	{
		$field = new tests\uncompleted\cli();

		$this->assert
			->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetOutputPrompt()
	{
		$field = new tests\uncompleted\cli();

		$this->assert
			->object($field->setOutputPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getOutputPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetOutputColorizer()
	{
		$field = new tests\uncompleted\cli();

		$this->assert
			->object($field->setOutputColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getOutputColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new tests\uncompleted\cli();

		$this
			->mock('mageekguy\atoum\runner')
			->assert
				->object($field->setWithRunner($runner = new mock\runner()))->isIdenticalTo($field)
				->object($field->getRunner())->isIdenticalTo($runner)
				->object($field->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($field)
				->object($field->getRunner())->isIdenticalTo($runner)
				->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
				->object($field->getRunner())->isIdenticalTo($runner)
		;
	}

	public function test__toString()
	{
		$this
			->mock('mageekguy\atoum\score')
			->mock('mageekguy\atoum\runner')
		;

		$runner = new mock\runner();
		$runner->getMockController()->getScore = $score = new mock\score();

		$defaultField = new tests\uncompleted\cli();
		$customField = new tests\uncompleted\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$score->getMockController()->getUncompletedTests = array();

		$this->assert('There is no uncompleted method')
			->castToString($defaultField)->isEmpty()
			->castToString($defaultField->setWithRunner($runner))->isEmpty()
			->castToString($defaultField->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($defaultField->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
			->castToString($customField)->isEmpty()
			->castToString($customField->setWithRunner($runner))->isEmpty()
			->castToString($customField->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($customField->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;


		$score->getMockController()->getUncompletedTests = $allUncompletedTests = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'exitCode' => $exitCode = rand(1, PHP_INT_MAX),
				'output' => $output = uniqid()
			),
			array(
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'exitCode' => $otherExitCode = rand(1, PHP_INT_MAX),
				'output' => ($otherOutputLine1 = uniqid()) . PHP_EOL . ($otherOutputLine2 = uniqid())
			),
			array(
				'class' => $anotherClass = uniqid(),
				'method' => $anotherMethod = uniqid(),
				'exitCode' => $anotherExitCode = rand(1, PHP_INT_MAX),
				'output' => ''
			)
		);

		$this->assert('There is uncompleted methods and no case')
			->castToString($defaultField-> setWithRunner($runner))->isEqualTo(sprintf('There are %d uncompleted methods:', sizeof($allUncompletedTests)) . PHP_EOL .
				sprintf('%s::%s() with exit code %d:', $class, $method, $exitCode) . PHP_EOL .
				'output(' . strlen($output) . ') "' . $output . '"' . PHP_EOL .
				sprintf('%s::%s() with exit code %d:', $otherClass, $otherMethod, $otherExitCode) . PHP_EOL .
				'output(' . (strlen($otherOutputLine1) + strlen($otherOutputLine2) + 1) . ') "' . $otherOutputLine1 . PHP_EOL .
				$otherOutputLine2 . '"' . PHP_EOL .
				sprintf('%s::%s() with exit code %d:', $anotherClass, $anotherMethod, $anotherExitCode) . PHP_EOL .
				'output(0) ""' . PHP_EOL
			)
			->castToString($customField->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d uncompleted method', 'There are %d uncompleted methods', sizeof($allUncompletedTests)), sizeof($allUncompletedTests)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $class, $method, $exitCode))
				) .
				PHP_EOL .
				$outputPrompt .
				'output(' . strlen($output) . ') "' . $output . '"' .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $otherClass, $otherMethod, $otherExitCode))
				) .
				PHP_EOL .
				$outputPrompt .
				'output(' . (strlen($otherOutputLine1) + strlen($otherOutputLine2) + 1) . ') "' . $otherOutputLine1 .
				PHP_EOL .
				$outputPrompt .
				$otherOutputLine2 . '"' .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize(sprintf('%s::%s() with exit code %d', $anotherClass, $anotherMethod, $anotherExitCode))
				) .
				PHP_EOL .
				$outputPrompt .
				'output(0) ""' .
				PHP_EOL
			)
		;
	}
}

?>
