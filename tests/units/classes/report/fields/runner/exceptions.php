<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class exceptions extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(runner\exceptions::titlePrompt)->isEqualTo('> ')
			->string(runner\exceptions::methodPrompt)->isEqualTo('=> ')
			->string(runner\exceptions::exceptionPrompt)->isEqualTo('==> ')
		;
	}

	public function test__construct()
	{
		$exceptions = new runner\exceptions();

		$this->assert
			->object($exceptions)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($exceptions->getRunner())->isNull()
			->object($exceptions->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$exceptions = new runner\exceptions();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($exceptions->setWithRunner($runner))->isIdenticalTo($exceptions)
			->object($exceptions->getRunner())->isIdenticalTo($runner)
			->object($exceptions->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($exceptions)
			->object($exceptions->getRunner())->isIdenticalTo($runner)
			->object($exceptions->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($exceptions)
			->object($exceptions->getRunner())->isIdenticalTo($runner)
		;
	}

	public function testToString()
	{
		$exceptions = new runner\exceptions($locale = new atoum\locale());

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
			->string($exceptions->toString())->isEmpty()
			->string($exceptions->setWithRunner($runner)->toString())->isEmpty()
			->string($exceptions->setWithRunner($runner, atoum\runner::runStart)->toString())->isEmpty()
			->string($exceptions->setWithRunner($runner, atoum\runner::runStop)->toString())->isEmpty()
		;

		$exceptionss = array(
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

		$score->getMockController()->getExceptions = function() use ($exceptionss) { return $exceptionss; };

		$exceptions = new runner\exceptions($locale = new atoum\locale());

		$this->assert
			->string($exceptions->toString())->isEmpty()
			->string($exceptions->setWithRunner($runner)->toString())->isEqualTo(runner\exceptions::titlePrompt . sprintf($locale->__('There is %d exception:', 'There are %d exceptions:', sizeof($exceptionss)), sizeof($exceptionss)) . PHP_EOL .
				runner\exceptions::methodPrompt . $class . '::' . $method . '():' . PHP_EOL .
				runner\exceptions::exceptionPrompt . sprintf($locale->_('Exception throwed in file %s on line %d:'), $file, $line) . PHP_EOL .
				$value . PHP_EOL .
				runner\exceptions::methodPrompt . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				runner\exceptions::exceptionPrompt . sprintf($locale->_('Exception throwed in file %s on line %d:'), $otherFile, $otherLine) . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;
	}
}

?>
