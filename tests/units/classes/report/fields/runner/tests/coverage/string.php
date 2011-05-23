<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\score,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner\tests
;

require_once(__DIR__ . '/../../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\tests\coverage
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
			->string(tests\coverage\string::defaultTitlePrompt)->isEqualTo('> ')
			->string(tests\coverage\string::defaultClassPrompt)->isEqualTo('=> ')
			->string(tests\coverage\string::defaultMethodPrompt)->isEqualTo('==> ')
		;
	}

	public function test__construct()
	{
		$field = new tests\coverage\string();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getTitlePrompt())->isEqualTo(tests\coverage\string::defaultTitlePrompt)
			->string($field->getClassPrompt())->isEqualTo(tests\coverage\string::defaultClassPrompt)
			->string($field->getMethodPrompt())->isEqualTo(tests\coverage\string::defaultMethodPrompt)
			->variable($field->getCoverage())->isNull()
		;

		$field = new tests\coverage\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $classPrompt = uniqid(), $methodPrompt = uniqid());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getTitlePrompt())->isEqualTo($titlePrompt)
			->string($field->getClassPrompt())->isEqualTo($classPrompt)
			->string($field->getMethodPrompt())->isEqualTo($methodPrompt)
			->variable($field->getCoverage())->isNull()
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new tests\coverage\string();

		$this->assert
			->object($field->setTitlePrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo($prompt)
			->object($field->setTitlePrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new tests\coverage\string();

		$this->assert
			->object($field->setClassPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getClassPrompt())->isEqualTo($prompt)
			->object($field->setClassPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getClassPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetClassPrompt()
	{
		$field = new tests\coverage\string();

		$this->assert
			->object($field->setMethodPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo($prompt)
			->object($field->setMethodPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new tests\coverage\string($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$scoreCoverage = new score\coverage();

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getCoverage = function() use ($scoreCoverage) { return $scoreCoverage; };

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function () use ($score) { return $score; };

		$this->assert
			->variable($field->getCoverage())->isNull()
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->variable($field->getCoverage())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($field)
			->variable($field->getCoverage())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
			->variable($field->getCoverage())->isIdenticalTo($scoreCoverage)
		;
	}

	public function test__toString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\reflectionClass')
			->generate('\reflectionMethod')
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$scoreCoverage = new score\coverage();

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getCoverage = function() use ($scoreCoverage) { return $scoreCoverage; };

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function () use ($score) { return $score; };

		$field = new tests\coverage\string($locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$classController = new mock\controller();
		$classController->__construct = function() {};
		$classController->getName = function() use (& $className) { return $className; };
		$classController->getFileName = function() use (& $classFile) { return $classFile; };

		$class = new mock\reflectionClass(uniqid(), $classController);

		$methodController = new mock\controller();
		$methodController->__construct = function() {};
		$methodController->isAbstract = false;
		$methodController->getFileName = function() use (& $classFile) { return $classFile; };
		$methodController->getDeclaringClass = $class;
		$methodController->getName = function() use (& $methodName) { return $methodName; };
		$methodController->getStartLine = 6;
		$methodController->getEndLine = 8;

		$classController->getMethods = array(new mock\reflectionMethod(uniqid(), uniqid(), $methodController));

		$scoreCoverage->setReflectionClassInjector(function($className) use ($class) { return $class; });

		$classFile = uniqid();
		$className = uniqid();
		$methodName = uniqid();

		$xdebugData = array(
		  $classFile =>
			 array(
				5 => 1,
				6 => 2,
				7 => 3,
				8 => 2,
				9 => 1
			),
		  uniqid() =>
			 array(
				5 => 2,
				6 => 3,
				7 => 4,
				8 => 3,
				9 => 2
			)
		);

		$scoreCoverage->addXdebugData($this, $xdebugData);

		$field = new tests\coverage\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(
					$field->getTitlePrompt() . sprintf($field->getLocale()->_('Code coverage value: %3.2f%%'), $scoreCoverage->getValue() * 100) . PHP_EOL .
					$field->getClassPrompt() . sprintf($field->getLocale()->_('Class %s: %3.2f%%'), $className, $scoreCoverage->getValueForClass($className) * 100.0) . PHP_EOL .
					$field->getMethodPrompt() . sprintf($field->getLocale()->_('%s::%s(): %3.2f%%'), $className, $methodName, $scoreCoverage->getValueForMethod($className, $methodName) * 100.0) . PHP_EOL
			)
		;

		$field = new tests\coverage\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $classPrompt = uniqid(), $methodPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(
					$field->getTitlePrompt() . sprintf($field->getLocale()->_('Code coverage value: %3.2f%%'), $scoreCoverage->getValue() * 100) . PHP_EOL .
					$field->getClassPrompt() . sprintf($field->getLocale()->_('Class %s: %3.2f%%'), $className, $scoreCoverage->getValueForClass($className) * 100.0) . PHP_EOL .
					$field->getMethodPrompt() . sprintf($field->getLocale()->_('%s::%s(): %3.2f%%'), $className, $methodName, $scoreCoverage->getValueForMethod($className, $methodName) * 100.0) . PHP_EOL
			)
		;
	}
}

?>
