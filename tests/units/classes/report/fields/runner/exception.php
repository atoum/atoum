<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class exception extends atoum\test
{
	public function test__construct()
	{
		$exception = new runner\exception();

		$this->assert
			->object($exception)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($exception->getRunner())->isNull()
			->object($exception->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$exception = new runner\exception();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($exception->setWithRunner($runner))->isIdenticalTo($exception)
			->object($exception->getRunner())->isIdenticalTo($runner)
			->object($exception->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($exception)
			->object($exception->getRunner())->isIdenticalTo($runner)
			->object($exception->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($exception)
			->object($exception->getRunner())->isIdenticalTo($runner)
		;
	}

	public function testToString()
	{
		$exception = new runner\exception($locale = new atoum\locale());

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
			->string($exception->toString())->isEmpty()
			->string($exception->setWithRunner($runner)->toString())->isEmpty()
			->string($exception->setWithRunner($runner, atoum\runner::runStart)->toString())->isEmpty()
			->string($exception->setWithRunner($runner, atoum\runner::runStop)->toString())->isEmpty()
		;

		$exceptions = array(
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

		$score->getMockController()->getExceptions = function() use ($exceptions) { return $exceptions; };

		$exception = new runner\exception($locale = new atoum\locale());

		$this->assert
			->string($exception->toString())->isEmpty()
			->string($exception->setWithRunner($runner)->toString())->isEqualTo(sprintf($locale->__('There is %d exception:', 'There are %d exceptions:', sizeof($exceptions)), sizeof($exceptions)) . PHP_EOL .
				'  ' . $class . '::' . $method . '():' . PHP_EOL .
				'    ' . sprintf($locale->_('Exception throwed in file %s on line %d:'), $file, $line) . PHP_EOL .
				'      ' . $value . PHP_EOL .
				'  ' . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				'    ' . sprintf($locale->_('Exception throwed in file %s on line %d:'), $otherFile, $otherLine) . PHP_EOL .
				'      ' . $firstOtherValue . PHP_EOL .
				'      ' . $secondOtherValue . PHP_EOL
			)
		;
	}
}

?>
