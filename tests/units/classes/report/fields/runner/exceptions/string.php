<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\exceptions;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\exceptions
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
			->string(runner\exceptions\string::defaultTitlePrompt)->isEqualTo('> ')
			->string(runner\exceptions\string::defaultMethodPrompt)->isEqualTo('=> ')
			->string(runner\exceptions\string::defaultExceptionPrompt)->isEqualTo('==> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\exceptions\string();

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getTitlePrompt())->isEqualTo(runner\exceptions\string::defaultTitlePrompt)
			->string($field->getMethodPrompt())->isEqualTo(runner\exceptions\string::defaultMethodPrompt)
			->string($field->getExceptionPrompt())->isEqualTo(runner\exceptions\string::defaultExceptionPrompt)
		;

		$field = new runner\exceptions\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $exceptionPrompt = uniqid());

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getTitlePrompt())->isEqualTo($titlePrompt)
			->string($field->getMethodPrompt())->isEqualTo($methodPrompt)
			->string($field->getExceptionPrompt())->isEqualTo($exceptionPrompt)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\exceptions\string();

		$this->assert
			->object($field->setTitlePrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo($prompt)
			->object($field->setTitlePrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new runner\exceptions\string();

		$this->assert
			->object($field->setMethodPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo($prompt)
			->object($field->setMethodPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetExceptionPrompt()
	{
		$field = new runner\exceptions\string();

		$this->assert
			->object($field->setExceptionPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getExceptionPrompt())->isEqualTo($prompt)
			->object($field->setExceptionPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getExceptionPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\exceptions\string();

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
		$field = new runner\exceptions\string($locale = new atoum\locale());

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

		$fields = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = uniqid(),
				'value' => $value = uniqid()
			),
			array(
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = uniqid(),
				'value' => ($firstOtherValue = uniqid()) . PHP_EOL . ($secondOtherValue = uniqid())
			),
		);

		$score->getMockController()->getExceptions = function() use ($fields) { return $fields; };

		$field = new runner\exceptions\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d exception:', 'There are %d exceptions:', sizeof($fields)), sizeof($fields)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getExceptionPrompt() . sprintf($locale->_('Exception throwed in file %s on line %d:'), $file, $line) . PHP_EOL .
				$value . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$field->getExceptionPrompt() . sprintf($locale->_('Exception throwed in file %s on line %d:'), $otherFile, $otherLine) . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;

		$field = new runner\exceptions\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $exceptionPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d exception:', 'There are %d exceptions:', sizeof($fields)), sizeof($fields)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getExceptionPrompt() . sprintf($locale->_('Exception throwed in file %s on line %d:'), $file, $line) . PHP_EOL .
				$value . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$field->getExceptionPrompt() . sprintf($locale->_('Exception throwed in file %s on line %d:'), $otherFile, $otherLine) . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;
	}
}

?>
