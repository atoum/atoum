<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\failures;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\runner\failures
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(runner\failures\cli::defaultTitlePrompt)->isEqualTo('> ')
			->string(runner\failures\cli::defaultMethodPrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\failures\cli();

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getTitlePrompt())->isEqualTo(runner\failures\cli::defaultTitlePrompt)
			->string($field->getMethodPrompt())->isEqualTo(runner\failures\cli::defaultMethodPrompt)
		;

		$field = new runner\failures\cli($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid());

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getTitlePrompt())->isEqualTo($titlePrompt)
			->string($field->getMethodPrompt())->isEqualTo($methodPrompt)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\failures\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo($prompt)
			->object($field->setTitlePrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new runner\failures\cli();

		$this->assert
			->object($field->setMethodPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo($prompt)
			->object($field->setMethodPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\failures\cli();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

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

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getErrors = function() { return array(); };

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$fails = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = uniqid(),
				'asserter' => $asserter = uniqid(),
				'fail' => $fail = uniqid()
			),
			array(
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = uniqid(),
				'asserter' => $otherAsserter = uniqid(),
				'fail' => $otherFail = uniqid()
			)
		);

		$score->getMockController()->getFailAssertions = function() use ($fails) { return $fails; };

		$field = new runner\failures\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d failure:', 'There are %d failures:', sizeof($fails)), sizeof($fails)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				sprintf($field->getLocale()->_('In file %s on line %d, %s failed : %s'), $file, $line, $asserter, $fail) . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				sprintf($field->getLocale()->_('In file %s on line %d, %s failed : %s'), $otherFile, $otherLine, $otherAsserter, $otherFail) . PHP_EOL
			)
		;

		$field = new runner\failures\cli($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d failure:', 'There are %d failures:', sizeof($fails)), sizeof($fails)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				sprintf($field->getLocale()->_('In file %s on line %d, %s failed : %s'), $file, $line, $asserter, $fail) . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				sprintf($field->getLocale()->_('In file %s on line %d, %s failed : %s'), $otherFile, $otherLine, $otherAsserter, $otherFail) . PHP_EOL
			)
		;
	}
}

?>
