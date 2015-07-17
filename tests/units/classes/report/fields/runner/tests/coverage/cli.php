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

class cli extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\tests\coverage');
	}

	public function test__construct()
	{
		$this
			->if($field = new tests\coverage\cli())
			->then
				->object($field->getTitlePrompt())->isEqualTo(new prompt())
				->object($field->getClassPrompt())->isEqualTo(new prompt())
				->object($field->getMethodPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getCoverageColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getCoverage())->isNull()
				->array($field->getEvents())->isEqualTo(array(atoum\runner::runStop))
		;
	}

	public function testSetTitlePrompt()
	{
		$this
			->if($field = new tests\coverage\cli())
			->then
				->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getTitlePrompt())->isEqualTo($prompt)
				->object($field->setTitlePrompt())->isIdenticalTo($field)
				->object($field->getTitlePrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetClassPrompt()
	{
		$this
			->if($field = new tests\coverage\cli())
			->then
				->object($field->setClassPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getClassPrompt())->isEqualTo($prompt)
				->object($field->setClassPrompt())->isIdenticalTo($field)
				->object($field->getClassPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetMethodPrompt()
	{
		$this
			->if($field = new tests\coverage\cli())
			->then
				->object($field->setMethodPrompt($prompt = new prompt()))->isIdenticalTo($field)
				->object($field->getMethodPrompt())->isEqualTo($prompt)
				->object($field->setMethodPrompt())->isIdenticalTo($field)
				->object($field->getMethodPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetTitleColorizer()
	{
		$this
			->if($field = new tests\coverage\cli())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
				->object($field->setTitleColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetTitleCoverageColorizer()
	{
		$this
			->if($field = new tests\coverage\cli())
			->then
				->object($field->setCoverageColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getCoverageColorizer())->isIdenticalTo($colorizer)
				->object($field->setCoverageColorizer())->isIdenticalTo($field)
				->object($field->getCoverageColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new tests\coverage\cli())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getCoverage())->isNull()
				->boolean($field->handleEvent(atoum\runner::runStop, $runner = new atoum\runner()))->isTrue()
				->object($field->getCoverage())->isIdenticalTo($runner->getScore()->getCoverage())
		;
	}

	public function testHideClassesCoverageDetails()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->hideClassesCoverageDetails())->isTestedInstance
		;
	}

	public function testHideMethodsCoverageDetails()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->hideMethodsCoverageDetails())->isTestedInstance
		;
	}

	public function test__toString()
	{
		$this
			->if($scoreCoverage = new score\coverage())
			->and($score = new \mock\mageekguy\atoum\runner\score())
			->and($score->getMockController()->getCoverage = function() use ($scoreCoverage) { return $scoreCoverage; })
			->and($runner = new atoum\runner())
			->and($runner->setScore($score))
			->and($defaultField = new tests\coverage\cli())
			->and($customField = new tests\coverage\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setClassPrompt($classPrompt = new prompt(uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setCoverageColorizer($coverageColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
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
			->and($classController->disableMethodChecking())
			->and($classController->__construct = function() {})
			->and($classController->getName = function() use (& $className) { return $className; })
			->and($classController->getFileName = function() use (& $classFile) { return $classFile; })
			->and($classController->getTraits = array())
			->and($classController->getStartLine = 1)
			->and($classController->getEndLine = 12)
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
			->and($className = uniqid())
			->and($methodName = uniqid())
			->and($scoreCoverage->setReflectionClassFactory(function() use ($class) { return $class; }))
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
			->and($defaultField = new tests\coverage\cli())
			->and($customField = new tests\coverage\cli())
			->and($customField->setTitlePrompt($titlePrompt = new prompt(uniqid())))
			->and($customField->setTitleColorizer($titleColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setClassPrompt($classPrompt = new prompt(uniqid())))
			->and($customField->setMethodPrompt($methodPrompt = new prompt(uniqid())))
			->and($customField->setCoverageColorizer($coverageColorizer = new colorizer(uniqid(), uniqid())))
			->and($customField->setLocale($locale = new locale()))
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
						$defaultField->getTitlePrompt() . sprintf($defaultField->getLocale()->_('Code coverage value: %3.2f%%'), $scoreCoverage->getValue() * 100) . PHP_EOL .
						$defaultField->getClassPrompt() . sprintf($defaultField->getLocale()->_('Class %s: %3.2f%%'), $className, $scoreCoverage->getValueForClass($className) * 100.0) . PHP_EOL .
						$defaultField->getMethodPrompt() . sprintf($defaultField->getLocale()->_('%s::%s(): %3.2f%%'), $className, $methodName, $scoreCoverage->getValueForMethod($className, $methodName) * 100.0) . PHP_EOL
					)
				->castToString($customField)->isEqualTo(
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
			->if($defaultField->hideMethodsCoverageDetails())
			->and($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo(
						$defaultField->getTitlePrompt() . sprintf($defaultField->getLocale()->_('Code coverage value: %3.2f%%'), $scoreCoverage->getValue() * 100) . PHP_EOL .
						$defaultField->getClassPrompt() . sprintf($defaultField->getLocale()->_('Class %s: %3.2f%%'), $className, $scoreCoverage->getValueForClass($className) * 100.0) . PHP_EOL
					)
			->if($defaultField->hideClassesCoverageDetails())
			->and($defaultField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($defaultField)->isEqualTo($defaultField->getTitlePrompt() . sprintf($defaultField->getLocale()->_('Code coverage value: %3.2f%%'), $scoreCoverage->getValue() * 100) . PHP_EOL)
			->if($customField->hideClassesCoverageDetails())
			->and($customField->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($customField)->isEqualTo($customField->getTitlePrompt() . sprintf($customField->getLocale()->_('Code coverage value: %3.2f%%'), $scoreCoverage->getValue() * 100) . PHP_EOL)
		;
	}
}
