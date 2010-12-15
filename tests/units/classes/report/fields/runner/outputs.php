<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class outputs extends atoum\test
{
	public function test__construct()
	{
		$outputs = new runner\outputs();

		$this->assert
			->object($outputs)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($outputs->getRunner())->isNull()
			->object($outputs->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$outputs = new runner\outputs();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($outputs->setWithRunner($runner))->isIdenticalTo($outputs)
			->object($outputs->getRunner())->isIdenticalTo($runner)
			->object($outputs->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($outputs)
			->object($outputs->getRunner())->isIdenticalTo($runner)
			->object($outputs->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($outputs)
			->object($outputs->getRunner())->isIdenticalTo($runner)
		;
	}

	public function testToString()
	{
		$outputs = new runner\outputs($locale = new atoum\locale());

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
			->castToString($outputs)->isEmpty()
			->castToString($outputs->setWithRunner($runner))->isEmpty()
			->castToString($outputs->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($outputs->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$outputss = array(
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

		$score->getMockController()->getOutputs = function() use ($outputss) { return $outputss; };

		$outputs = new runner\outputs($locale = new atoum\locale());

		$this->assert
			->castToString($outputs)->isEmpty()
			->castToString($outputs->setWithRunner($runner))->isEqualTo(runner\outputs::titlePrompt . sprintf($locale->__('There is %d output:', 'There are %d outputs:', sizeof($outputss)), sizeof($outputss)) . PHP_EOL .
				runner\outputs::methodPrompt . $class . '::' . $method . '():' . PHP_EOL .
				$value . PHP_EOL .
				runner\outputs::methodPrompt . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;
	}
}

?>
