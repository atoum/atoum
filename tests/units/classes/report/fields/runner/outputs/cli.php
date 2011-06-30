<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\outputs;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\runner\outputs
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
			->string(runner\outputs\cli::defaultTitlePrompt)->isEqualTo('> ')
			->string(runner\outputs\cli::defaultMethodPrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\outputs\cli();

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getTitlePrompt())->isEqualTo(runner\outputs\cli::defaultTitlePrompt)
			->string($field->getMethodPrompt())->isEqualTo(runner\outputs\cli::defaultMethodPrompt)
		;

		$field = new runner\outputs\cli($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid());

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getTitlePrompt())->isEqualTo($titlePrompt)
			->string($field->getMethodPrompt())->isEqualTo($methodPrompt)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\outputs\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo($prompt)
			->object($field->setTitlePrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new runner\outputs\cli();

		$this->assert
			->object($field->setMethodPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo($prompt)
			->object($field->setMethodPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\outputs\cli();

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
		$field = new runner\outputs\cli();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getOutputs = function() { return array(); };

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$fields = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'value' => $value = uniqid()
			),
			array(
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'value' => ($firstOtherValue = uniqid()) . PHP_EOL . ($secondOtherValue = uniqid())
			)
		);

		$score->getMockController()->getOutputs = function() use ($fields) { return $fields; };

		$field = new runner\outputs\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d output:', 'There are %d outputs:', sizeof($fields)), sizeof($fields)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$value . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;

		$field = new runner\outputs\cli($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d output:', 'There are %d outputs:', sizeof($fields)), sizeof($fields)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$value . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;
	}
}

?>
