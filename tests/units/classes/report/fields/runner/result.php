<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class result extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(runner\result::titlePrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$result = new runner\result();

		$this->assert
			->object($result)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($result->getTestNumber())->isNull()
			->variable($result->getTestMethodNumber())->isNull()
			->variable($result->getFailNumber())->isNull()
			->variable($result->getErrorNumber())->isNull()
			->variable($result->getExceptionNumber())->isNull()
		;
	}

	public function testSetWithRunner()
	{
		$result = new runner\result();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();

		$assertionNumber = rand(1, PHP_INT_MAX);
		$scoreController->getAssertionNumber = function() use ($assertionNumber) { return $assertionNumber; };

		$failNumber = rand(1, PHP_INT_MAX);
		$scoreController->getFailNumber = function() use ($failNumber) { return $failNumber; };

		$errorNumber = rand(1, PHP_INT_MAX);
		$scoreController->getErrorNumber = function() use ($errorNumber) { return $errorNumber; };

		$exceptionNumber = rand(1, PHP_INT_MAX);
		$scoreController->getExceptionNumber = function() use ($exceptionNumber) { return $exceptionNumber; };

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = function() use ($score) { return $score; };
		$runnerController->getTestNumber = function() use (& $testNumber) { return $testNumber = rand(1, PHP_INT_MAX); };
		$runnerController->getTestMethodNumber = function() use (& $testMethodNumber) { return $testMethodNumber = rand(1, PHP_INT_MAX); };


		$this->assert
			->object($result->setWithRunner($runner))->isIdenticalTo($result)
			->variable($result->getTestNumber())->isNull()
			->variable($result->getTestMethodNumber())->isNull()
			->variable($result->getAssertionNumber())->isNull()
			->variable($result->getFailNumber())->isNull()
			->variable($result->getErrorNumber())->isNull()
			->variable($result->getExceptionNumber())->isNull()
			->object($result->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($result)
			->variable($result->getTestNumber())->isNull()
			->variable($result->getTestMethodNumber())->isNull()
			->variable($result->getAssertionNumber())->isNull()
			->variable($result->getFailNumber())->isNull()
			->variable($result->getErrorNumber())->isNull()
			->variable($result->getExceptionNumber())->isNull()
			->object($result->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($result)
			->integer($result->getTestNumber())->isEqualTo($testNumber)
			->integer($result->getTestMethodNumber())->isEqualTo($testMethodNumber)
			->integer($result->getAssertionNumber())->isEqualTo($assertionNumber)
			->integer($result->getFailNumber())->isEqualTo($failNumber)
			->integer($result->getErrorNumber())->isEqualTo($errorNumber)
			->integer($result->getExceptionNumber())->isEqualTo($exceptionNumber)
		;
	}

	public function testToString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getAssertionNumber = function() use (& $assertionNumber) { return $assertionNumber = rand(1, PHP_INT_MAX); };
		$scoreController->getErrorNumber = function() use (& $errorNumber) { return $errorNumber = rand(0, PHP_INT_MAX); };
		$scoreController->getExceptionNumber = function() use (& $exceptionNumber) { return $exceptionNumber = rand(0, PHP_INT_MAX); };


		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = function() use ($score) { return $score; };
		$scoreController->getFailNumber = function() { return 0; };
		$runnerController->getTestNumber = function() use (& $testNumber) { return $testNumber = rand(1, PHP_INT_MAX); };
		$runnerController->getTestMethodNumber = function() use (& $testMethodNumber) { return $testMethodNumber = rand(1, PHP_INT_MAX); };

		$result = new runner\result($locale = new atoum\locale());

		$this->assert
			->castToString($result)->isEqualTo(runner\result::titlePrompt . $locale->_('No test running.') . PHP_EOL)
			->castToString($result->setWithRunner($runner))->isEqualTo(runner\result::titlePrompt . $locale->_('No test running.') . PHP_EOL)
			->castToString($result->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(runner\result::titlePrompt . $locale->_('No test running.') . PHP_EOL)
			->castToString($result->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(runner\result::titlePrompt . sprintf($locale->_('Success (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($locale->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($locale->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($locale->__('%s assertion', '%s assertions', $assertionNumber), $assertionNumber),
				sprintf($locale->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($locale->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;

		$scoreController->getFailNumber = function() use (& $failNumber) { return $failNumber = rand(1, PHP_INT_MAX); };

		$result = new runner\result($locale = new atoum\locale());

		$this->assert
			->castToString($result)->isEqualTo(runner\result::titlePrompt . $locale->_('No test running.') . PHP_EOL)
			->castToString($result->setWithRunner($runner))->isEqualTo(runner\result::titlePrompt . $locale->_('No test running.') . PHP_EOL)
			->castToString($result->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(runner\result::titlePrompt . $locale->_('No test running.') . PHP_EOL)
			->castToString($result->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(runner\result::titlePrompt . sprintf($locale->_('Failure (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($locale->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($locale->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($locale->__('%s failure', '%s failures', $failNumber), $failNumber),
				sprintf($locale->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($locale->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;
	}
}

?>
