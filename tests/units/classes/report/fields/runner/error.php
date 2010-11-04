<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class error extends atoum\test
{
	public function test__construct()
	{
		$error = new runner\error();

		$this->assert
			->object($error)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($error->getRunner())->isNull()
			->object($error->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$error = new runner\error();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($error->setWithRunner($runner))->isIdenticalTo($error)
			->object($error->getRunner())->isIdenticalTo($runner)
			->object($error->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($error)
			->object($error->getRunner())->isIdenticalTo($runner)
			->object($error->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($error)
			->object($error->getRunner())->isIdenticalTo($runner)
		;
	}

	public function testToString()
	{
		$error = new runner\error($locale = new atoum\locale());

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
			->string($error->toString())->isEmpty()
			->string($error->setWithRunner($runner)->toString())->isEmpty()
			->string($error->setWithRunner($runner, atoum\runner::runStart)->toString())->isEmpty()
			->string($error->setWithRunner($runner, atoum\runner::runStop)->toString())->isEmpty()
		;

		$errors = array(
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

		$score->getMockController()->getErrors = function() use ($errors) { return $errors; };

		$error = new runner\error($locale = new atoum\locale());

		$this->assert
			->string($error->toString())->isEmpty()
			->string($error->setWithRunner($runner)->toString())->isEqualTo(sprintf($locale->__('There is %d error:', 'There are %d errors:', sizeof($errors)), sizeof($errors)) . PHP_EOL .
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
