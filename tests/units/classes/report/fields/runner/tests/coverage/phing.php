<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests\coverage;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\score,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\tests
;

require_once __DIR__ . '/../../../../../../runner.php';

class phing extends atoum\test
{
	public function testClass()
	{
		$this->assert->testedClass->isSubclassOf('mageekguy\atoum\report\fields\runner\tests\coverage\cli');
	}

	public function test__construct()
	{
		$this->assert
			->if($field = new tests\coverage\phing())
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getClassPrompt())->isEqualTo(new prompt())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getCoverageColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getCoverage())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
				->boolean($field->missingCodeCoverageIsShowed())->isTrue()
			->if($field = new tests\coverage\phing(null, null, null, null, null, null))
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getClassPrompt())->isEqualTo(new prompt())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getCoverageColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getCoverage())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
				->boolean($field->missingCodeCoverageIsShowed())->isTrue()
			->if($field = new tests\coverage\phing($titlePrompt = new prompt(), $classPrompt = new prompt(), $methodPrompt = new prompt(), $titleColorizer = new colorizer(), $coverageColorizer = new colorizer(), $locale = new locale(), false))
			->then
				->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
				->object($field->getClassPrompt())->isIdenticalTo($classPrompt)
				->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
				->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
				->object($field->getCoverageColorizer())->isIdenticalTo($coverageColorizer)
				->object($field->getLocale())->isIdenticalTo($locale)
				->variable($field->getCoverage())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
				->boolean($field->missingCodeCoverageIsShowed())->isFalse()
		;
	}

	public function testSetTitlePrompt()
	{
		$this->assert
			->if($field = new tests\coverage\phing())
			->then
				->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isEqualTo($prompt)
			->if($field = new tests\coverage\phing(new prompt()))
			->then
				->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isEqualTo($prompt)
		;
	}

	public function testSetClassPrompt()
	{
		$this->assert
			->if($field = new tests\coverage\phing())
			->then
				->object($field->setMethodPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isEqualTo($prompt)
			->if($field = new tests\coverage\phing(null, new prompt()))
			->then
				->object($field->setMethodPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetMethodPrompt()
	{
		$this->assert
			->if($field = new tests\coverage\phing())
			->then
				->object($field->setClassPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getClassPrompt())->isEqualTo($prompt)
			->if($field = new tests\coverage\phing(null, null, new prompt()))
			->then
				->object($field->setClassPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getClassPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$this->assert
			->if($field = new tests\coverage\phing())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
			->if($field = new tests\coverage\phing(null, null, null, new colorizer()))
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetTitleCoverageColorizer()
	{
		$this->assert
			->if($field = new tests\coverage\phing())
			->then
				->object($field->setCoverageColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getCoverageColorizer())->isIdenticalTo($colorizer)
			->if($field = new tests\coverage\phing(null, null, null, null, new colorizer()))
			->then
				->object($field->setCoverageColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getCoverageColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testShowMissingCodeCoverage()
	{
		$this->assert
			->if($field = new tests\coverage\phing())
			->then
				->object($field->showMissingCodeCoverage(true))->isIdenticalTo($field)
				->boolean($field->missingCodeCoverageIsShowed())->isTrue()
				->object($field->showMissingCodeCoverage(false))->isIdenticalTo($field)
				->boolean($field->missingCodeCoverageIsShowed())->isFalse()
		;
	}

	public function testHandleEvent()
	{
		$this->assert
			->if($field = new tests\coverage\phing())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getCoverage())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $runner = new atoum\runner()))->isTrue()
				->object($field->getCoverage())->isIdenticalTo($runner->getScore()->getCoverage())
		;
	}

	public function test__toString()
	{
		$this
			->assert
				->if($scoreCoverage = new score\coverage($factory = new atoum\factory()))
				->and($score = new \mock\mageekguy\atoum\score())
				->and($score->getMockController()->getCoverage = function() use ($scoreCoverage) { return $scoreCoverage; })
				->and($runner = new atoum\runner())
				->and($runner->setScore($score))
				->and($defaultField = new tests\coverage\phing())
				->and($customField = new tests\coverage\phing($titlePrompt = new prompt(uniqid()), $classPrompt = new prompt(uniqid()), $methodPrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $coverageColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStart, $runner))
				->and($customField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
				->and($customField->handleEvent(atoum\runner::runStop, $runner))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($classController = new mock\controller())
				->and($classController->__construct = function() {})
				->and($classController->getName = function() use (& $className) { return $className; })
				->and($classController->getFileName = function() use (& $classFile) { return $classFile; })
				->and($class = new \mock\reflectionClass(uniqid(), $classController))
				->and($methodController = new mock\controller())
				->and($methodController->__construct = function() {})
				->and($methodController->isAbstract = false)
				->and($methodController->getFileName = function() use (& $classFile) { return $classFile; })
				->and($methodController->getDeclaringClass = $class)
				->and($methodController->getName = function() use (& $methodName) { return $methodName; })
				->and($methodController->getStartLine = 6)
				->and($methodController->getEndLine = 8)
				->and($classController->getMethods = array(new \mock\reflectionMethod(uniqid(), uniqid(), $methodController)))
				->and($factory['reflectionClass'] = $class)
				->and($className = uniqid())
				->and($methodName = uniqid())
				->and($scoreCoverage->addXdebugDataForTest($this, $xdebugData = array(
							  ($classFile = uniqid()) =>
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
							)
						)
					)
				->and($defaultField = new tests\coverage\phing())
				->and($customField = new tests\coverage\phing($titlePrompt = new prompt(uniqid()), $classPrompt = new prompt(uniqid()), $methodPrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $coverageColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale()))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStart, $runner))
				->and($customField->handleEvent(atoum\runner::runStart, $runner))
				->then
					->castToString($defaultField)->isEmpty()
					->castToString($customField)->isEmpty()
				->if($defaultField->handleEvent(atoum\runner::runStop, $runner))
				->and($customField->handleEvent(atoum\runner::runStop, $runner))
				->then
					->castToString($defaultField)->isEqualTo(
							$defaultField->getTitlePrompt() . sprintf($defaultField->getLocale()->_('Code coverage value : %3.2f%%'), $scoreCoverage->getValue() * 100) . PHP_EOL .
							$defaultField->getClassPrompt() . sprintf($defaultField->getLocale()->_('Class %s : %3.2f%%'), $className, $scoreCoverage->getValueForClass($className) * 100.0) . PHP_EOL .
							$defaultField->getMethodPrompt() . sprintf($defaultField->getLocale()->_('     ::%s() : %3.2f%%'), $methodName, $scoreCoverage->getValueForMethod($className, $methodName) * 100.0) . PHP_EOL
						)
					->castToString($customField)->isEqualTo(
							$titlePrompt .
							sprintf(
								$locale->_('%s : %s'),
								$titleColorizer->colorize($locale->_('Code coverage value')),
								$coverageColorizer->colorize(sprintf('%3.2f%%', $scoreCoverage->getValue() * 100.0))
							) .
							PHP_EOL .
							$classPrompt .
							sprintf(
								$locale->_('%s : %s'),
								$titleColorizer->colorize(sprintf($locale->_('Class %s'), $className)),
								$coverageColorizer->colorize(sprintf('%3.2f%%', $scoreCoverage->getValueForClass($className) * 100.0))
							) .
							PHP_EOL .
							$methodPrompt .
							sprintf(
								$locale->_('%s : %s'),
								$titleColorizer->colorize(sprintf($locale->_('     ::%s()'), $methodName)),
								$coverageColorizer->colorize(sprintf('%3.2f%%', $scoreCoverage->getValueForClass($className, $methodName) * 100.0))
							) .
							PHP_EOL
						)
		;
	}
}
