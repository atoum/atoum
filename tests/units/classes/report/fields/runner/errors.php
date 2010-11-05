<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class errors extends atoum\test
{
	public function test__construct()
	{
		$errors = new runner\errors();

		$this->assert
			->object($errors)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($errors->getRunner())->isNull()
			->object($errors->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$errors = new runner\errors();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($errors->setWithRunner($runner))->isIdenticalTo($errors)
			->object($errors->getRunner())->isIdenticalTo($runner)
			->object($errors->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($errors)
			->object($errors->getRunner())->isIdenticalTo($runner)
			->object($errors->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($errors)
			->object($errors->getRunner())->isIdenticalTo($runner)
		;
	}

	public function testToString()
	{
		$errors = new runner\errors($locale = new atoum\locale());

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
			->string($errors->toString())->isEmpty()
			->string($errors->setWithRunner($runner)->toString())->isEmpty()
			->string($errors->setWithRunner($runner, atoum\runner::runStart)->toString())->isEmpty()
			->string($errors->setWithRunner($runner, atoum\runner::runStop)->toString())->isEmpty()
		;

		$errorss = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = uniqid(),
				'type' => $type = uniqid(),
				'message' => $message = uniqid()
			),
			array(
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = uniqid(),
				'type' => $otherType = uniqid(),
				'message' => ($firstOtherMessage = uniqid()) . PHP_EOL . ($secondOtherMessage = uniqid())
			),
		);

		$score->getMockController()->getErrors = function() use ($errorss) { return $errorss; };

		$errors = new runner\errors($locale = new atoum\locale());

		$this->assert
			->string($errors->toString())->isEmpty()
			->string($errors->setWithRunner($runner)->toString())->isEqualTo(sprintf($locale->__('There is %d error:', 'There are %d errors:', sizeof($errorss)), sizeof($errorss)) . PHP_EOL .
				'  ' . $class . '::' . $method . '():' . PHP_EOL .
				'    ' . sprintf($locale->_('Error %s in file %s on line %d:'), $type, $file, $line) . PHP_EOL .
				'      ' . $message . PHP_EOL .
				'  ' . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				'    ' . sprintf($locale->_('Error %s in file %s on line %d:'), $otherType, $otherFile, $otherLine) . PHP_EOL .
				'      ' . $firstOtherMessage . PHP_EOL .
				'      ' . $secondOtherMessage . PHP_EOL
			)
		;
	}
}

?>
