<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\score,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report\fields\runner\tests
;

require_once(__DIR__ . '/../../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\runner\tests\coverage
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function test__construct()
	{
		$field = new tests\coverage\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getClassPrompt())->isEqualTo(new prompt())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getCoverageColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getCoverage())->isNull()
		;

		$field = new tests\coverage\cli(null, null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getClassPrompt())->isEqualTo(new prompt())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getCoverageColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getCoverage())->isNull()
		;


		$field = new tests\coverage\cli($titlePrompt = new prompt(), $classPrompt = new prompt(), $methodPrompt = new prompt(), $titleColorizer = new colorizer(), $coverageColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getClassPrompt())->isIdenticalTo($classPrompt)
			->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getCoverageColorizer())->isIdenticalTo($coverageColorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getCoverage())->isNull()
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new tests\coverage\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isEqualTo($prompt)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new tests\coverage\cli();

		$this->assert
			->object($field->setClassPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getClassPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetClassPrompt()
	{
		$field = new tests\coverage\cli();

		$this->assert
			->object($field->setMethodPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getMethodPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new tests\coverage\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetTitleCoverageColorizer()
	{
		$field = new tests\coverage\cli();

		$this->assert
			->object($field->setCoverageColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getCoverageColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new tests\coverage\cli();

		$this->mock
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
		$this->mock
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

		$field = new tests\coverage\cli();

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

		$scoreCoverage->addXdebugDataForTest($this, $xdebugData);

		$field = new tests\coverage\cli();

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

		$field = new tests\coverage\cli($titlePrompt = new prompt(uniqid()), $classPrompt = new prompt(uniqid()), $methodPrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $coverageColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(
					$titlePrompt .
					sprintf(
						$locale->_('%s: %s'),
						$titleColorizer->colorize($locale->_('Code coverage value')),
						$coverageColorizer->colorize(sprintf('%3.2f%%', $scoreCoverage->getValue() * 100.0))
					) .
					PHP_EOL .
					$classPrompt .
					sprintf(
						$locale->_('%s: %s'),
						$titleColorizer->colorize(sprintf($locale->_('Class %s'), $className)),
						$coverageColorizer->colorize(sprintf('%3.2f%%', $scoreCoverage->getValueForClass($className) * 100.0))
					) .
					PHP_EOL .
					$methodPrompt .
					sprintf(
						$locale->_('%s: %s'),
						$titleColorizer->colorize(sprintf($locale->_('%s::%s()'), $className, $methodName)),
						$coverageColorizer->colorize(sprintf('%3.2f%%', $scoreCoverage->getValueForClass($className, $methodName) * 100.0))
					) .
					PHP_EOL
			)
		;
	}
}

?>
