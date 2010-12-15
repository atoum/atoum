<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\score;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner\tests;

require_once(__DIR__ . '/../../../../../runner.php');

class coverage extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(tests\coverage::titlePrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$duration = new tests\coverage();

		$this->assert
			->object($duration)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($duration->getCoverage())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$coverage = new tests\coverage($locale = new atoum\locale());

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
			->variable($coverage->getCoverage())->isNull()
			->object($coverage->setWithRunner($runner))->isIdenticalTo($coverage)
			->variable($coverage->getCoverage())->isNull()
			->object($coverage->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($coverage)
			->variable($coverage->getCoverage())->isNull()
			->object($coverage->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($coverage)
			->variable($coverage->getCoverage())->isIdenticalTo($scoreCoverage)
		;
	}

	public function testToString()
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

		$coverage = new tests\coverage($locale = new atoum\locale());

		$this->assert
			->castToString($coverage)->isEmpty()
			->castToString($coverage->setWithRunner($runner))->isEmpty()
			->castToString($coverage->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($coverage->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$methodController = new mock\controller();
		$methodController->__construct = function() {};
		$methodController->isAbstract = false;
		$methodController->getFileName = function() use (& $classFile) { return $classFile; };
		$methodController->getName = function() use (& $methodName) { return $methodName; };
		$methodController->getStartLine = 6;
		$methodController->getEndLine = 8;

		$classController = new mock\controller();
		$classController->__construct = function() {};
		$classController->getName = function() use (& $className) { return $className; };
		$classController->getFileName = function() use (& $classFile) { return $classFile; };
		$classController->getMethods = array(new mock\reflectionMethod(uniqid(), uniqid(), $methodController));

		$scoreCoverage->setReflectionClassInjector(function($class) use ($classController) { return new mock\reflectionClass($class, $classController); });

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

		$coverage = new tests\coverage($locale = new atoum\locale());

		$scoreCoverage->addXdebugData($this, $xdebugData);

		$this->assert
			->castToString($coverage)->isEmpty()
			->castToString($coverage->setWithRunner($runner))->isEmpty()
			->castToString($coverage->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($coverage->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(
					tests\coverage::titlePrompt . sprintf($locale->_('Code coverage value: %3.2f%%'), $scoreCoverage->getValue() * 100) . PHP_EOL .
					tests\coverage::classPrompt . sprintf($locale->_('Class %s: %3.2f%%'), $className, $scoreCoverage->getValueForClass($className) * 100.0) . PHP_EOL .
					tests\coverage::methodPrompt . sprintf($locale->_('%s::%s(): %3.2f%%'), $className, $methodName, $scoreCoverage->getValueForMethod($className, $methodName) * 100.0) . PHP_EOL
			)
		;
	}
}

?>
