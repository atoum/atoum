<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\result;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner,
	mageekguy\atoum\tests\units
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\runner
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubClassOf('mageekguy\atoum\report\field')
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

		$this->mockGenerator
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\runner')
		;

		$score = new \mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();

		$scoreController->getAssertionNumber = $assertionNumber = rand(1, PHP_INT_MAX);

		$scoreController->getFailNumber = $failNumber = rand(1, PHP_INT_MAX);

		$scoreController->getErrorNumber = $errorNumber = rand(1, PHP_INT_MAX);

		$scoreController->getExceptionNumber = $exceptionNumber = rand(1, PHP_INT_MAX);

		$runner = new \mock\mageekguy\atoum\runner();
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
		$this->mockGenerator
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\runner')
			->generate('mageekguy\atoum\locale')
			->generate('mageekguy\atoum\cli\prompt')
			->generate('mageekguy\atoum\cli\colorizer')
		;

		$score = new \mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getAssertionNumber = 1;
		$scoreController->getFailNumber = 0;
		$scoreController->getErrorNumber = 0;
		$scoreController->getExceptionNumber = 0;

		$runner = new \mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;
		$runnerController->getTestNumber = 1;
		$runnerController->getTestMethodNumber = 1;

		$locale = new \mock\mageekguy\atoum\locale();
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

		$prompt = new \mock\mageekguy\atoum\cli\prompt();
		$promptController = $prompt->getMockController();
		$promptController->__toString = $promptString = uniqid();

		$successColorizer = new \mock\mageekguy\atoum\cli\colorizer();
		$successColorizerController = $successColorizer->getMockController();
		$successColorizerController->colorize = $colorizedSuccessString = uniqid();

		$failureColorizer = new \mock\mageekguy\atoum\cli\colorizer();
		$failureColorizerController = $failureColorizer->getMockController();
		$failureColorizerController->colorize = $colorizedFailureString = uniqid();

		$this->startCase('Success with one test, one method and one assertion, no fail, no error, no exception');

		$field = new runner\result\cli($prompt, $successColorizer, $failureColorizer, $locale);

		$this->assert
			->castToString($field)->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($promptString . $colorizedSuccessString . PHP_EOL)
			->mock($locale)
				->call('__')->withArguments('%s test', '%s tests', 1)->once()
				->call('__')->withArguments('%s method', '%s methods', 1)->once()
				->call('__')->withArguments('%s assertion', '%s assertions', 1)->once()
				->call('__')->withArguments('%s error', '%s errors', 0)->once()
				->call('__')->withArguments('%s exception', '%s exceptions', 0)->once()
				->call('_')->withArguments('Success (%s, %s, %s, %s, %s) !')->once()
			->mock($successColorizer)
				->call('colorize')->withArguments($noTestRunningString)->never()
				->call('colorize')->withArguments($successString)->once()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->startCase('Success with several tests, several methods and several assertions,  no fail, no error, no exception');

		$runnerController->getTestNumber = $testNumber = rand(2, PHP_INT_MAX);
		$runnerController->getTestMethodNumber = $testMethodNumber = rand(2, PHP_INT_MAX);
		$scoreController->getAssertionNumber = $assertionNumber = rand(2, PHP_INT_MAX);

		$field = new runner\result\cli($prompt, $successColorizer, $failureColorizer, $locale);

		$this->assert
			->castToString($field)->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($promptString . $colorizedSuccessString . PHP_EOL)
			->mock($locale)
				->call('__')->withArguments('%s test', '%s tests', $testNumber)->once()
				->call('__')->withArguments('%s method', '%s methods', $testMethodNumber)->once()
				->call('__')->withArguments('%s assertion', '%s assertions', $assertionNumber)->once()
				->call('__')->withArguments('%s error', '%s errors', 0)->once()
				->call('__')->withArguments('%s exception', '%s exceptions', 0)->once()
				->call('_')->withArguments('Success (%s, %s, %s, %s, %s) !')->once()
			->mock($successColorizer)
				->call('colorize')->withArguments($noTestRunningString)->never()
				->call('colorize')->withArguments($successString)->once()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->startCase('Failure with several tests, several methods and several assertions, one fail, one error, one exception');

		$scoreController->getFailNumber = 1;
		$scoreController->getErrorNumber = 1;
		$scoreController->getExceptionNumber = 1;

		$field = new runner\result\cli($prompt, $successColorizer, $failureColorizer, $locale);

		$this->assert
			->castToString($field)->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($promptString . $colorizedFailureString . PHP_EOL)
			->mock($locale)
				->call('__')->withArguments('%s test', '%s tests', $testNumber)->once()
				->call('__')->withArguments('%s method', '%s methods', $testMethodNumber)->once()
				->call('__')->withArguments('%s failure', '%s failures', 1)->once()
				->call('__')->withArguments('%s error', '%s errors', 1)->once()
				->call('__')->withArguments('%s exception', '%s exceptions', 1)->once()
				->call('_')->withArguments('Failure (%s, %s, %s, %s, %s) !')->once()
			->mock($failureColorizer)
				->call('colorize')->withArguments($noTestRunningString)->never()
				->call('colorize')->withArguments($failureString)->once()
			->mock($successColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->startCase('Failure with several tests, several methods and several assertions, several fails, several errors, several exceptions');

		$scoreController->getFailNumber = $failNumber = rand(2, PHP_INT_MAX);
		$scoreController->getErrorNumber = $errorNumber = rand(2, PHP_INT_MAX);
		$scoreController->getExceptionNumber = $exceptionNumber = rand(2, PHP_INT_MAX);

		$field = new runner\result\cli($prompt, $successColorizer, $failureColorizer, $locale);

		$this->assert
			->castToString($field)->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($promptString . $noTestRunningString . PHP_EOL)
			->mock($locale)->call('_')->withArguments('No test running.')->once()
			->mock($successColorizer)->call('colorize')->withArguments($noTestRunningString)->never()
			->mock($failureColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;

		$this->assert
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($promptString . $colorizedFailureString . PHP_EOL)
			->mock($locale)
				->call('__')->withArguments('%s test', '%s tests', $testNumber)->once()
				->call('__')->withArguments('%s method', '%s methods', $testMethodNumber)->once()
				->call('__')->withArguments('%s failure', '%s failures', $failNumber)->once()
				->call('__')->withArguments('%s error', '%s errors', $errorNumber)->once()
				->call('__')->withArguments('%s exception', '%s exceptions', $exceptionNumber)->once()
				->call('_')->withArguments('Failure (%s, %s, %s, %s, %s) !')->once()
			->mock($failureColorizer)
				->call('colorize')->withArguments($noTestRunningString)->never()
				->call('colorize')->withArguments($failureString)->once()
			->mock($successColorizer)->call('colorize')->never()
			->mock($prompt)->call('__toString')->once()
		;
	}
}

?>
