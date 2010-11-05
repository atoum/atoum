<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class failures extends atoum\test
{
	public function test__construct()
	{
		$failures = new runner\failures();

		$this->assert
			->object($failures)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($failures->getRunner())->isNull()
			->object($failures->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$failures = new runner\failures();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($failures->setWithRunner($runner))->isIdenticalTo($failures)
			->object($failures->getRunner())->isIdenticalTo($runner)
			->object($failures->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($failures)
			->object($failures->getRunner())->isIdenticalTo($runner)
			->object($failures->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($failures)
			->object($failures->getRunner())->isIdenticalTo($runner)
		;
	}

	public function testToString()
	{
		$failures = new runner\failures($locale = new atoum\locale());

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
			->string($failures->toString())->isEmpty()
			->string($failures->setWithRunner($runner)->toString())->isEmpty()
			->string($failures->setWithRunner($runner, atoum\runner::runStart)->toString())->isEmpty()
			->string($failures->setWithRunner($runner, atoum\runner::runStop)->toString())->isEmpty()
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

		$failures = new runner\failures($locale = new atoum\locale());

		$this->assert
			->string($failures->toString())->isEmpty()
			->string($failures->setWithRunner($runner)->toString())->isEqualTo(sprintf($locale->__('There is %d failure:', 'There are %d failures:', sizeof($fails)), sizeof($fails)) . PHP_EOL .
				'  ' . $class . '::' . $method . '():' . PHP_EOL .
				'    ' . sprintf($locale->_('In file %s on line %d, %s failed because %s'), $file, $line, $asserter, $fail) . PHP_EOL .
				'  ' . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				'    ' . sprintf($locale->_('In file %s on line %d, %s failed because %s'), $otherFile, $otherLine, $otherAsserter, $otherFail) . PHP_EOL
			)
		;
	}
}

?>
