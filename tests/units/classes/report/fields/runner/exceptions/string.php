<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\exceptions;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\exceptions
{
	public function testClassConstants()
	{
		$this->assert
			->string(runner\exceptions\string::titlePrompt)->isEqualTo('> ')
			->string(runner\exceptions\string::methodPrompt)->isEqualTo('=> ')
			->string(runner\exceptions\string::exceptionPrompt)->isEqualTo('==> ')
		;
	}

	public function test__construct()
	{
		$exceptions = new runner\exceptions\string();

		$this->assert
			->object($exceptions)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($exceptions->getRunner())->isNull()
			->object($exceptions->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$exceptions = new runner\exceptions\string();

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

	public function test__toString()
	{
		$exceptions = new runner\exceptions\string($locale = new atoum\locale());

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
			->castToString($exceptions)->isEmpty()
			->castToString($exceptions->setWithRunner($runner))->isEmpty()
			->castToString($exceptions->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($exceptions->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
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

		$exceptions = new runner\exceptions\string($locale = new atoum\locale());

		$this->assert
			->castToString($exceptions)->isEmpty()
			->castToString($exceptions->setWithRunner($runner))->isEqualTo(runner\exceptions\string::titlePrompt . sprintf($locale->__('There is %d exception:', 'There are %d exceptions:', sizeof($exceptionss)), sizeof($exceptionss)) . PHP_EOL .
				runner\exceptions\string::methodPrompt . $class . '::' . $method . '():' . PHP_EOL .
				runner\exceptions\string::exceptionPrompt . sprintf($locale->_('Exception throwed in file %s on line %d:'), $file, $line) . PHP_EOL .
				$value . PHP_EOL .
				runner\exceptions\string::methodPrompt . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				runner\exceptions\string::exceptionPrompt . sprintf($locale->_('Exception throwed in file %s on line %d:'), $otherFile, $otherLine) . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;
	}
}

?>
