<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\result;

use
	\mageekguy\atoum,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report\fields\runner,
	\mageekguy\atoum\tests\units,
	\mageekguy\atoum\mock\mageekguy\atoum as mock
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubClassOf('\mageekguy\atoum\report\field')
		;
	}

	public function test__construct()
	{
		$field = new runner\result\cli();

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getSuccessColorizer())->isEqualTo(new colorizer())
			->object($field->getFailureColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getTestNumber())->isNull()
			->variable($field->getTestMethodNumber())->isNull()
			->variable($field->getFailNumber())->isNull()
			->variable($field->getErrorNumber())->isNull()
			->variable($field->getExceptionNumber())->isNull()
		;

		$field = new runner\result\cli(null, null, null, null);

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getSuccessColorizer())->isEqualTo(new colorizer())
			->object($field->getFailureColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getTestNumber())->isNull()
			->variable($field->getTestMethodNumber())->isNull()
			->variable($field->getFailNumber())->isNull()
			->variable($field->getErrorNumber())->isNull()
			->variable($field->getExceptionNumber())->isNull()
		;

		$field = new runner\result\cli($prompt = new prompt(), $successColorizer = new colorizer(), $failureColorizer = new colorizer(), $locale = new locale());

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
			->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetSuccessColorizer()
	{
		$field = new runner\result\cli();

		$this->assert
			->object($field->setSuccessColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getSuccessColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetFailureColorizer()
	{
		$field = new runner\result\cli();

		$this->assert
			->object($field->setFailureColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getFailureColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\result\cli();

		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\score();
		$scoreController = $score->getMockController();

		$scoreController->getAssertionNumber = $assertionNumber = rand(1, PHP_INT_MAX);

		$scoreController->getFailNumber = $failNumber = rand(1, PHP_INT_MAX);

		$scoreController->getErrorNumber = $errorNumber = rand(1, PHP_INT_MAX);

		$scoreController->getExceptionNumber = $exceptionNumber = rand(1, PHP_INT_MAX);

		$runner = new mock\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;
		$runnerController->getTestNumber = $testNumber = rand(1, PHP_INT_MAX);
		$runnerController->getTestMethodNumber = $testMethodNumber = rand(1, PHP_INT_MAX);


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

	public function test__toString()
	{
		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
			->generate('\mageekguy\atoum\locale')
			->generate('\mageekguy\atoum\cli\prompt')
			->generate('\mageekguy\atoum\cli\colorizer')
		;

		$score = new mock\score();
		$scoreController = $score->getMockController();
		$scoreController->getAssertionNumber = 1;
		$scoreController->getFailNumber = 0;
		$scoreController->getErrorNumber = 0;
		$scoreController->getExceptionNumber = 0;

		$runner = new mock\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;
		$runnerController->getTestNumber = 1;
		$runnerController->getTestMethodNumber = 1;

		$locale = new mock\locale();
		$localeController = $locale->getMockController();
		$localeController->_ = function ($string) use (& $noTestRunningString, & $successString, & $failureString) {
			switch ($string)
			{
				case 'No test running.':
					return $noTestRunningString = uniqid();

				case 'Success (%s, %s, %s, %s, %s) !':
					return $successString = uniqid();

				case 'Failure (%s, %s, %s, %s, %s) !':
					return $failureString = uniqid();

				default:
					return uniqid();
			}
		};
		$localeController->__ = function($singularString, $pluralString, $number) use (& $testString, & $testMethodString, & $assertionString, & $errorString, & $exceptionString) {
			switch ($singularString)
			{
				case '%d test':
					return $testString = uniqid();

				case '%d method':
					return $testMethodString = uniqid();

				case '%d assertion':
					return $assertionString = uniqid();

				case '%d error':
					return $errorString = uniqid();

				case '%d exception':
					return $exceptionString = uniqid();

				default:
					return uniqid();
			}
		};

		$prompt = new mock\cli\prompt();
		$promptController = $prompt->getMockController();
		$promptController->__toString = $promptString = uniqid();

		$successColorizer = new mock\cli\colorizer();
		$successColorizerController = $successColorizer->getMockController();
		$successColorizerController->colorize = $colorizedSuccessString = uniqid();

		$failureColorizer = new mock\cli\colorizer();
		$failureColorizerController = $failureColorizer->getMockController();
		$failureColorizerController->colorize = $colorizedFailureString = uniqid();

		$this->startCase('Success with one test, one method and one assertion, no fail, no error, no exception');

		$field = new runner\result\cli($prompt, $successColorizer, $failureColorizer, $locale);

		$this->assert
			->castToString($field)->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($promptString . $colorizedSuccessString . PHP_EOL)
			->mock($locale)
				->call('__', array('%s test', '%s tests', 1))
				->call('__', array('%s method', '%s methods', 1))
				->call('__', array('%s assertion', '%s assertions', 1))
				->call('__', array('%s error', '%s errors', 0))
				->call('__', array('%s exception', '%s exceptions', 0))
				->call('_', array('Success (%s, %s, %s, %s, %s) !'))
			->mock($successColorizer)
				->notCall('colorize', array($noTestRunningString))
				->call('colorize', array($successString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->startCase('Success with several tests, several methods and several assertions,  no fail, no error, no exception');

		$runnerController->getTestNumber = $testNumber = rand(2, PHP_INT_MAX);
		$runnerController->getTestMethodNumber = $testMethodNumber = rand(2, PHP_INT_MAX);
		$scoreController->getAssertionNumber = $assertionNumber = rand(2, PHP_INT_MAX);

		$field = new runner\result\cli($prompt, $successColorizer, $failureColorizer, $locale);

		$this->assert
			->castToString($field)->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($promptString . $colorizedSuccessString . PHP_EOL)
			->mock($locale)
				->call('__', array('%s test', '%s tests', $testNumber))
				->call('__', array('%s method', '%s methods', $testMethodNumber))
				->call('__', array('%s assertion', '%s assertions', $assertionNumber))
				->call('__', array('%s error', '%s errors', 0))
				->call('__', array('%s exception', '%s exceptions', 0))
				->call('_', array('Success (%s, %s, %s, %s, %s) !'))
			->mock($successColorizer)
				->notCall('colorize', array($noTestRunningString))
				->call('colorize', array($successString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->startCase('Failure with several tests, several methods and several assertions, one fail, one error, one exception');

		$scoreController->getFailNumber = 1;
		$scoreController->getErrorNumber = 1;
		$scoreController->getExceptionNumber = 1;

		$field = new runner\result\cli($prompt, $successColorizer, $failureColorizer, $locale);

		$this->assert
			->castToString($field)->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($promptString . $colorizedFailureString . PHP_EOL)
			->mock($locale)
				->call('__', array('%s test', '%s tests', $testNumber))
				->call('__', array('%s method', '%s methods', $testMethodNumber))
				->call('__', array('%s failure', '%s failures', 1))
				->call('__', array('%s error', '%s errors', 1))
				->call('__', array('%s exception', '%s exceptions', 1))
				->call('_', array('Failure (%s, %s, %s, %s, %s) !'))
			->mock($failureColorizer)
				->notCall('colorize', array($noTestRunningString))
				->call('colorize', array($failureString))
			->mock($successColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->startCase('Failure with several tests, several methods and several assertions, several fails, several errors, several exceptions');

		$scoreController->getFailNumber = $failNumber = rand(2, PHP_INT_MAX);
		$scoreController->getErrorNumber = $errorNumber = rand(2, PHP_INT_MAX);
		$scoreController->getExceptionNumber = $exceptionNumber = rand(2, PHP_INT_MAX);

		$field = new runner\result\cli($prompt, $successColorizer, $failureColorizer, $locale);

		$this->assert
			->castToString($field)->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_', array('No test running.'))
			->mock($successColorizer)->notCall('colorize', array($noTestRunningString))
			->mock($failureColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($promptString . $colorizedFailureString . PHP_EOL)
			->mock($locale)
				->call('__', array('%s test', '%s tests', $testNumber))
				->call('__', array('%s method', '%s methods', $testMethodNumber))
				->call('__', array('%s failure', '%s failures', $failNumber))
				->call('__', array('%s error', '%s errors', $errorNumber))
				->call('__', array('%s exception', '%s exceptions', $exceptionNumber))
				->call('_', array('Failure (%s, %s, %s, %s, %s) !'))
			->mock($failureColorizer)
				->notCall('colorize', array($noTestRunningString))
				->call('colorize', array($failureString))
			->mock($successColorizer)->wasNotCalled()
			->mock($prompt)->call('__toString')
		;
	}
}

?>
