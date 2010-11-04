<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class output extends atoum\test
{
	public function test__construct()
	{
		$output = new runner\output();

		$this->assert
			->object($output)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($output->getRunner())->isNull()
			->object($output->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$output = new runner\output();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($output->setWithRunner($runner))->isIdenticalTo($output)
			->object($output->getRunner())->isIdenticalTo($runner)
			->object($output->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($output)
			->object($output->getRunner())->isIdenticalTo($runner)
			->object($output->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($output)
			->object($output->getRunner())->isIdenticalTo($runner)
		;
	}

	public function testToString()
	{
		$output = new runner\output($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getOutputs = function() { return array(); };

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->string($output->toString())->isEmpty()
			->string($output->setWithRunner($runner)->toString())->isEmpty()
			->string($output->setWithRunner($runner, atoum\runner::runStart)->toString())->isEmpty()
			->string($output->setWithRunner($runner, atoum\runner::runStop)->toString())->isEmpty()
		;

		$outputs = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'value' => $value = uniqid()
			),
			array(
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'value' => ($firstOtherValue = uniqid()) . PHP_EOL . ($secondOtherValue = uniqid())
			)
		);

		$score->getMockController()->getOutputs = function() use ($outputs) { return $outputs; };

		$output = new runner\output($locale = new atoum\locale());

		$this->assert
			->string($output->toString())->isEmpty()
			->string($output->setWithRunner($runner)->toString())->isEqualTo(sprintf($locale->__('There is %d output:', 'There are %d outputs:', sizeof($outputs)), sizeof($outputs)) . PHP_EOL .
				"  " . $class . '::' . $method . '():' . PHP_EOL .
				"    " . $value . PHP_EOL .
				"  " . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				"    " . $firstOtherValue . PHP_EOL .
				"    " . $secondOtherValue . PHP_EOL
			)
		;
	}
}

?>
