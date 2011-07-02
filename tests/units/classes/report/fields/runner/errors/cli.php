<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\errors;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner,
	\mageekguy\atoum\tests\units
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\runner\errors
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(runner\errors\cli::defaultTitlePrompt)->isEqualTo('> ')
			->string(runner\errors\cli::defaultMethodPrompt)->isEqualTo('=> ')
			->string(runner\errors\cli::defaultErrorPrompt)->isEqualTo('==> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\errors\cli();

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($field->getTitlePrompt())->isEqualTo(new prompt(runner\errors\cli::defaultTitlePrompt))
			->object($field->getTitleColorizer())->isEqualTo(new colorizer('0;31'))
			->object($field->getMethodPrompt())->isEqualTo(new prompt(runner\errors\cli::defaultMethodPrompt, new colorizer('0;31')))
			->object($field->getMethodColorizer())->isEqualTo(new colorizer('0;31'))
			->object($field->getErrorPrompt())->isEqualTo(new prompt(runner\errors\cli::defaultErrorPrompt, new colorizer('0;31')))
			->object($field->getErrorColorizer())->isEqualTo(new colorizer())
		;

		$field = new runner\errors\cli(null, null, null, null, null, null, null);

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($field->getTitlePrompt())->isEqualTo(new prompt(runner\errors\cli::defaultTitlePrompt))
			->object($field->getTitleColorizer())->isEqualTo(new colorizer('0;31'))
			->object($field->getMethodPrompt())->isEqualTo(new prompt(runner\errors\cli::defaultMethodPrompt, new colorizer('0;31')))
			->object($field->getMethodColorizer())->isEqualTo(new colorizer('0;31'))
			->object($field->getErrorPrompt())->isEqualTo(new prompt(runner\errors\cli::defaultErrorPrompt, new colorizer('0;31')))
			->object($field->getErrorColorizer())->isEqualTo(new colorizer())
		;

		$field = new runner\errors\cli ($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(), $locale = new atoum\locale());

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
			->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
			->object($field->getErrorPrompt())->isIdenticalTo($errorPrompt)
			->object($field->getErrorColorizer())->isIdenticalTo($errorColorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\errors\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new runner\errors\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new runner\errors\cli();

		$this->assert
			->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getMethodPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetMethodColorizer()
	{
		$field = new runner\errors\cli();

		$this->assert
			->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetErrorPrompt()
	{
		$field = new runner\errors\cli();

		$this->assert
			->object($field->setErrorPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getErrorPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetErrorColorizer()
	{
		$field = new runner\errors\cli();

		$this->assert
			->object($field->setErrorColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getErrorColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\errors\cli();

		$this->mock
			->generate('\mageekguy\atoum\runner')
		;

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->object($field->getRunner())->isIdenticalTo($runner)
			->object($field->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($field)
			->object($field->getRunner())->isIdenticalTo($runner)
			->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
			->object($field->getRunner())->isIdenticalTo($runner)
		;
	}

	public function test__toString()
	{
		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$field = new runner\errors\cli();

		$this->startCase('There is no error in score with default prompts');

		$score->getMockController()->getErrors = array();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$this->startCase('There is errors with file, line and no case in score with default prompts');

		$allErrors = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
			),
			array(
				'case' => null,
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = rand(1, PHP_INT_MAX),
				'type' => $otherType = uniqid(),
				'message' => ($firstOtherMessage = uniqid()) . PHP_EOL . ($secondOtherMessage = uniqid()),
				'errorFile' => $otherErrorFile = uniqid(),
				'errorLine' => $otherErrorLine = rand(1, PHP_INT_MAX),
			),
		);

		$score->getMockController()->getErrors = $allErrors;

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d:'), $type, $file, $line, $errorFile, $errorLine)) . PHP_EOL .
				$message . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($otherClass . '::' . $otherMethod . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d:'), $otherType, $otherFile, $otherLine, $otherErrorFile, $otherErrorLine)) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $errorColorizer->colorize(sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d:'), $type, $file, $line, $errorFile, $errorLine)) . PHP_EOL .
				$message . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($otherClass . '::' . $otherMethod . '():') . PHP_EOL .
				$field->getErrorPrompt() . $errorColorizer->colorize(sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d:'), $otherType, $otherFile, $otherLine, $otherErrorFile, $otherErrorLine)) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$this->startCase('There is errors with file, line and case in score with default prompts');

		$allErrors = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
			),
			array(
				'case' => $otherCase = uniqid(),
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = rand(1, PHP_INT_MAX),
				'type' => $otherType = uniqid(),
				'message' => ($firstOtherMessage = uniqid()) . PHP_EOL . ($secondOtherMessage = uniqid()),
				'errorFile' => $otherErrorFile = uniqid(),
				'errorLine' => $otherErrorLine = rand(1, PHP_INT_MAX),
			),
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':'), $type, $file, $line, $errorFile, $errorLine, $case)) . PHP_EOL .
				$message . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($otherClass . '::' . $otherMethod . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':'), $otherType, $otherFile, $otherLine, $otherErrorFile, $otherErrorLine, $otherCase)) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':'), $type, $file, $line, $errorFile, $errorLine, $case)) . PHP_EOL .
				$message . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($otherClass . '::' . $otherMethod . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':'), $otherType, $otherFile, $otherLine, $otherErrorFile, $otherErrorLine, $otherCase)) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$this->startCase('There is errors with no file, no line and no case in score with default prompts');

		$allErrors = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d:'), $type, $errorFile, $errorLine)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $errorColorizer->colorize(sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d:'), $type, $errorFile, $errorLine)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with no file, no line and case in score with default prompts');

		$allErrors = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $errorFile, $errorLine, $case)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $errorColorizer->colorize(sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $errorFile, $errorLine, $case)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with no file, line and no case in score with default prompts');

		$allErrors = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d:'), $type, $errorFile, $errorLine)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $errorColorizer->colorize(sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d:'), $type, $errorFile, $errorLine)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with no file, line and case in score with default prompts');

		$allErrors = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $errorFile, $errorLine, $case)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $errorColorizer->colorize(sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $errorFile, $errorLine, $case)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with file, no line and no case in score with default prompts');

		$allErrors = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in %s on unknown line, generated by file %s on line %d:'), $type, $file, $errorFile, $errorLine)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $errorColorizer->colorize(sprintf($field->getLocale()->_('Error %s in %s on unknown line, generated by file %s on line %d:'), $type, $file, $errorFile, $errorLine)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with file, no line and case in score with default prompts');

		$allErrors = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX),
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $field->getErrorColorizer()->colorize(sprintf($field->getLocale()->_('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $file, $errorFile, $errorLine, $case)) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors))) . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getErrorPrompt() . $errorColorizer->colorize(sprintf($field->getLocale()->_('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $file, $errorFile, $errorLine, $case)) . PHP_EOL .
				$message . PHP_EOL
			)
		;
	}
}

?>
