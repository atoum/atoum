<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

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
		$field = new runner\failures\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getMethodColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getRunner())->isNull()
		;

		$field = new runner\failures\cli(null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getMethodColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getRunner())->isNull()
		;

		$field = new runner\failures\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(), $locale = new atoum\locale());

		$this->assert
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
			->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\failures\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new runner\failures\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new runner\failures\cli();

		$this->assert
			->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getMethodPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetMethodColorizer()
	{
		$field = new runner\failures\cli();

		$this->assert
			->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\failures\cli();

		$this->mockGenerator
			->generate('mageekguy\atoum\runner');
		;

		$runner = new \mock\mageekguy\atoum\runner();

		$this->assert
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->object($field->getRunner())->isIdenticalTo($runner)
			->object($field->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($field)
			->object($field->getRunner())->isIdenticalTo($runner)
			->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
			->object($field->getRunner())->isIdenticalTo($runner)
		;
	}

	public function test__toString()
	{
		$field = new runner\failures\cli();

		$this->mockGenerator
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\runner')
		;

		$score = new \mock\mageekguy\atoum\score();
		$score->getMockController()->getErrors = array();

		$runner = new \mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$score->getMockController()->getFailAssertions = $fails = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = uniqid(),
				'asserter' => $asserter = uniqid(),
				'fail' => $fail = uniqid()
			),
			array(
				'case' => null,
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = uniqid(),
				'asserter' => $otherAsserter = uniqid(),
				'fail' => $otherFail = uniqid()
			)
		);

		$field = new runner\failures\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There are %d failures:', sizeof($fails)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('In file %s on line %d, %s failed: %s', $file, $line, $asserter, $fail) . PHP_EOL .
				$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				sprintf('In file %s on line %d, %s failed: %s', $otherFile, $otherLine, $otherAsserter, $otherFail) . PHP_EOL
			)
		;

		$field = new runner\failures\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d failure', 'There are %d failures', sizeof($fails)), sizeof($fails)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				sprintf($locale->_('In file %s on line %d, %s failed: %s'), $file, $line, $asserter, $fail) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
				) .
				PHP_EOL .
				sprintf($locale->_('In file %s on line %d, %s failed: %s'), $otherFile, $otherLine, $otherAsserter, $otherFail) .
				PHP_EOL
			)
		;

		$score->getMockController()->getFailAssertions = $fails = array(
			array(
				'case' => $case =  uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = uniqid(),
				'asserter' => $asserter = uniqid(),
				'fail' => $fail = uniqid()
			),
			array(
				'case' => null,
				'case' => $otherCase =  uniqid(),
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = uniqid(),
				'asserter' => $otherAsserter = uniqid(),
				'fail' => $otherFail = uniqid()
			)
		);

		$field = new runner\failures\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There are %d failures:', sizeof($fails)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('In file %s on line %d in case \'%s\', %s failed: %s', $file, $line, $case, $asserter, $fail) . PHP_EOL .
				$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				sprintf('In file %s on line %d in case \'%s\', %s failed: %s', $otherFile, $otherLine, $otherCase, $otherAsserter, $otherFail) . PHP_EOL
			)
		;
	}
}

?>
