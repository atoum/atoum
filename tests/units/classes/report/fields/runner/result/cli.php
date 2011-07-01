<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\result;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\runner\result
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubClassOf('\mageekguy\atoum\report\field')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(runner\result\cli::defaultPrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\result\cli();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getTestNumber())->isNull()
			->variable($field->getTestMethodNumber())->isNull()
			->variable($field->getFailNumber())->isNull()
			->variable($field->getErrorNumber())->isNull()
			->variable($field->getExceptionNumber())->isNull()
			->object($field->getPrompt())->isEqualTo(new atoum\cli\prompt(runner\result\cli::defaultPrompt))
			->object($field->getSuccessColorizer())->isInstanceOf('\mageekguy\atoum\cli\colorizer')
			->variable($field->getSuccessColorizer()->getForeground())->isNull()
			->variable($field->getSuccessColorizer()->getBackground())->isNull()
			->object($field->getFailureColorizer())->isInstanceOf('\mageekguy\atoum\cli\colorizer')
			->variable($field->getFailureColorizer()->getForeground())->isNull()
			->variable($field->getFailureColorizer()->getBackground())->isNull()
		;

		$field = new runner\result\cli(null, null, null, null);

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getTestNumber())->isNull()
			->variable($field->getTestMethodNumber())->isNull()
			->variable($field->getFailNumber())->isNull()
			->variable($field->getErrorNumber())->isNull()
			->variable($field->getExceptionNumber())->isNull()
			->object($field->getPrompt())->isEqualTo(new atoum\cli\prompt(runner\result\cli::defaultPrompt))
			->object($field->getSuccessColorizer())->isInstanceOf('\mageekguy\atoum\cli\colorizer')
			->variable($field->getSuccessColorizer()->getForeground())->isNull()
			->variable($field->getSuccessColorizer()->getBackground())->isNull()
			->object($field->getFailureColorizer())->isInstanceOf('\mageekguy\atoum\cli\colorizer')
			->variable($field->getFailureColorizer()->getForeground())->isNull()
			->variable($field->getFailureColorizer()->getBackground())->isNull()
		;

		$field = new runner\result\cli($successColorizer = new atoum\cli\colorizer(), $failureColorizer = new atoum\cli\colorizer(), $prompt = new atoum\cli\prompt(uniqid()), $locale = new atoum\locale());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getTestNumber())->isNull()
			->variable($field->getTestMethodNumber())->isNull()
			->variable($field->getFailNumber())->isNull()
			->variable($field->getErrorNumber())->isNull()
			->variable($field->getExceptionNumber())->isNull()
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getSuccessColorizer())->isIdenticalTo($successColorizer)
			->object($field->getFailureColorizer())->isIdenticalTo($failureColorizer)
		;
	}

	public function testSetPrompt()
	{
		$field = new runner\result\cli();

		$this->assert
			->object($field->setPrompt($prompt = new atoum\cli\prompt()))->isIdenticalTo($field)
			->object($field->getPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\result\cli();

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
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->variable($field->getTestNumber())->isNull()
			->variable($field->getTestMethodNumber())->isNull()
			->variable($field->getAssertionNumber())->isNull()
			->variable($field->getFailNumber())->isNull()
			->variable($field->getErrorNumber())->isNull()
			->variable($field->getExceptionNumber())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($field)
			->variable($field->getTestNumber())->isNull()
			->variable($field->getTestMethodNumber())->isNull()
			->variable($field->getAssertionNumber())->isNull()
			->variable($field->getFailNumber())->isNull()
			->variable($field->getErrorNumber())->isNull()
			->variable($field->getExceptionNumber())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
			->integer($field->getTestNumber())->isEqualTo($testNumber)
			->integer($field->getTestMethodNumber())->isEqualTo($testMethodNumber)
			->integer($field->getAssertionNumber())->isEqualTo($assertionNumber)
			->integer($field->getFailNumber())->isEqualTo($failNumber)
			->integer($field->getErrorNumber())->isEqualTo($errorNumber)
			->integer($field->getExceptionNumber())->isEqualTo($exceptionNumber)
		;
	}

	public function testSetSuccessColorizer()
	{
		$field = new runner\result\cli();

		$this->assert
			->object($field->setSuccessColorizer($colorizer = new atoum\cli\colorizer()))->isIdenticalTo($field)
			->object($field->getSuccessColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetFailureColorizer()
	{
		$field = new runner\result\cli();

		$this->assert
			->object($field->setFailureColorizer($colorizer = new atoum\cli\colorizer()))->isIdenticalTo($field)
			->object($field->getFailureColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function test__toString()
	{
		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getAssertionNumber = $assertionNumber = rand(1, PHP_INT_MAX);
		$scoreController->getFailNumber = $failNumber = 0;
		$scoreController->getErrorNumber = $errorNumber = 0;
		$scoreController->getExceptionNumber = $exceptionNumber = 0;

		$runner = new mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;
		$runnerController->getTestNumber = $testNumber = rand(1, PHP_INT_MAX);
		$runnerController->getTestMethodNumber = $testMethodNumber = rand(1, PHP_INT_MAX);

		$field = new runner\result\cli(null, null, null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $locale->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $locale->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $locale->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($locale->_('Success (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($locale->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($locale->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($locale->__('%s assertion', '%s assertions', $assertionNumber), $assertionNumber),
				sprintf($locale->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($locale->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;

		$field = new runner\result\cli($colorizer = new atoum\cli\colorizer(uniqid(), uniqid()), null, null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $locale->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $locale->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $locale->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . $colorizer->colorize(sprintf($locale->_('Success (%s, %s, %s, %s, %s) !'),
						sprintf($locale->__('%s test', '%s tests', $testNumber), $testNumber),
						sprintf($locale->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
						sprintf($locale->__('%s assertion', '%s assertions', $assertionNumber), $assertionNumber),
						sprintf($locale->__('%s error', '%s errors', $errorNumber), $errorNumber),
						sprintf($locale->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber)
					)
				) . PHP_EOL
			)
		;

		$scoreController->getErrorNumber = $errorNumber = rand(1, PHP_INT_MAX);

		$field = new runner\result\cli(null, null, null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Failure (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($field->getLocale()->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($field->getLocale()->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($field->getLocale()->__('%s failure', '%s failures', $failNumber), $failNumber),
				sprintf($field->getLocale()->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($field->getLocale()->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;

		$field = new runner\result\cli(null, $colorizer = new atoum\cli\colorizer(uniqid(), uniqid()), null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . $colorizer->colorize(sprintf($field->getLocale()->_('Failure (%s, %s, %s, %s, %s) !'),
					sprintf($field->getLocale()->__('%s test', '%s tests', $testNumber), $testNumber),
					sprintf($field->getLocale()->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
					sprintf($field->getLocale()->__('%s failure', '%s failures', $failNumber), $failNumber),
					sprintf($field->getLocale()->__('%s error', '%s errors', $errorNumber), $errorNumber),
					sprintf($field->getLocale()->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber)
					)
				) . PHP_EOL
			)
		;

		$scoreController->getErrorNumber = $errorNumber = 0;
		$scoreController->getExceptionNumber = $exceptionNumber = rand(1, PHP_INT_MAX);

		$field = new runner\result\cli(null, null, null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Failure (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($field->getLocale()->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($field->getLocale()->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($field->getLocale()->__('%s failure', '%s failures', $failNumber), $failNumber),
				sprintf($field->getLocale()->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($field->getLocale()->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;

		$scoreController->getErrorNumber = $errorNumber = rand(1, PHP_INT_MAX);
		$scoreController->getExceptionNumber = $exceptionNumber = rand(1, PHP_INT_MAX);

		$field = new runner\result\cli(null, null, null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Failure (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($field->getLocale()->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($field->getLocale()->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($field->getLocale()->__('%s failure', '%s failures', $failNumber), $failNumber),
				sprintf($field->getLocale()->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($field->getLocale()->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;

		$scoreController->getErrorNumber = $errorNumber = 0;
		$scoreController->getExceptionNumber = $exceptionNumber = 0;
		$scoreController->getFailNumber = $failNumber = rand(1, PHP_INT_MAX);

		$field = new runner\result\cli(null, null, null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Failure (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($field->getLocale()->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($field->getLocale()->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($field->getLocale()->__('%s failure', '%s failures', $failNumber), $failNumber),
				sprintf($field->getLocale()->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($field->getLocale()->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;

		$scoreController->getErrorNumber = $errorNumber = rand(1, PHP_INT_MAX);
		$scoreController->getExceptionNumber = $exceptionNumber = 0;
		$scoreController->getFailNumber = $failNumber = rand(1, PHP_INT_MAX);

		$field = new runner\result\cli(null, null, null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Failure (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($field->getLocale()->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($field->getLocale()->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($field->getLocale()->__('%s failure', '%s failures', $failNumber), $failNumber),
				sprintf($field->getLocale()->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($field->getLocale()->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;

		$scoreController->getErrorNumber = $errorNumber = rand(1, PHP_INT_MAX);
		$scoreController->getExceptionNumber = $exceptionNumber = rand(1, PHP_INT_MAX);
		$scoreController->getFailNumber = $failNumber = rand(1, PHP_INT_MAX);

		$field = new runner\result\cli(null, null, null, $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('No test running.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Failure (%s, %s, %s, %s, %s) !') . PHP_EOL,
				sprintf($field->getLocale()->__('%s test', '%s tests', $testNumber), $testNumber),
				sprintf($field->getLocale()->__('%s method', '%s methods', $testMethodNumber), $testMethodNumber),
				sprintf($field->getLocale()->__('%s failure', '%s failures', $failNumber), $failNumber),
				sprintf($field->getLocale()->__('%s error', '%s errors', $errorNumber), $errorNumber),
				sprintf($field->getLocale()->__('%s exception', '%s exceptions', $exceptionNumber), $exceptionNumber))
			)
		;
	}
}

?>
