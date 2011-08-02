<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\errors;

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
			->class($this->getTestedClassName())->isSubclassOf('mageekguy\atoum\report\fields\runner')
		;
	}

	public function test__construct()
	{
		$field = new runner\errors\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getMethodColorizer())->isEqualTo(new colorizer())
			->object($field->getErrorPrompt())->isEqualTo(new prompt())
			->object($field->getErrorColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getRunner())->isNull()
		;

		$field = new runner\errors\cli(null, null, null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getMethodColorizer())->isEqualTo(new colorizer())
			->object($field->getErrorPrompt())->isEqualTo(new prompt())
			->object($field->getErrorColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getRunner())->isNull()
		;

		$field = new runner\errors\cli ($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(), $locale = new atoum\locale());

		$this->assert
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
			->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
			->object($field->getErrorPrompt())->isIdenticalTo($errorPrompt)
			->object($field->getErrorColorizer())->isIdenticalTo($errorColorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getRunner())->isNull()
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

		$this->mockGenerator
			->generate('mageekguy\atoum\runner')
		;

		$runner = new \mock\mageekguy\atoum\runner();

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
		$this->mockGenerator
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\runner')
		;

		$score = new \mock\mageekguy\atoum\score();

		$runner = new \mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$this->startCase('There is no error in score with default prompts');

		$field = new runner\errors\cli();

		$score->getMockController()->getErrors = array();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$this->startCase('There is errors with file, line and no case in score with default prompts');

		$score->getMockController()->getErrors = $allErrors = array(
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

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There are %d errors:', sizeof($allErrors)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('Error %s in %s on line %d, generated by file %s on line %d:', strtoupper($type), $file, $line, $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL .
				$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				sprintf('Error %s in %s on line %d, generated by file %s on line %d:', strtoupper($otherType), $otherFile, $otherLine, $otherErrorFile, $otherErrorLine) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on line %d, generated by file %s on line %d'), strtoupper($type), $file, $line, $errorFile, $errorLine))
				) .
				PHP_EOL .
				$message . PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on line %d, generated by file %s on line %d'), strtoupper($otherType), $otherFile, $otherLine, $otherErrorFile, $otherErrorLine))
				) .
				PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$this->startCase('There is errors with file, line and case in score with default prompts');

		$score->getMockController()->getErrors = $allErrors = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
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
				'errorLine' => $otherErrorLine = rand(1, PHP_INT_MAX)
			),
		);

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There are %d errors:', sizeof($allErrors)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':', strtoupper($type), $file, $line, $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL .
				$otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				sprintf('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':', strtoupper($otherType), $otherFile, $otherLine, $otherErrorFile, $otherErrorLine, $otherCase) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\''), strtoupper($type), $file, $line, $errorFile, $errorLine, $case))
				) .
				PHP_EOL .
				$message . PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($otherClass . '::' . $otherMethod . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\''), strtoupper($otherType), $otherFile, $otherLine, $otherErrorFile, $otherErrorLine, $otherCase))
				) .
				PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$this->startCase('There is errors with no file, no line and no case in score with default prompts');

		$score->getMockController()->getErrors = $allErrors = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
			)
		);

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('Error %s in unknown file on unknown line, generated by file %s on line %d:', strtoupper($type), $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d'), strtoupper($type), $errorFile, $errorLine))
				) .
				PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with no file, no line and case in score with default prompts');

		$score->getMockController()->getErrors = $allErrors = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
			)
		);


		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':', strtoupper($type), $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\''), strtoupper($type), $errorFile, $errorLine, $case))
				) .
				PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with no file, line and no case in score with default prompts');

		$score->getMockController()->getErrors = $allErrors = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
			)
		);

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('Error %s in unknown file on unknown line, generated by file %s on line %d:', strtoupper($type), $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d'), strtoupper($type), $errorFile, $errorLine))
				) .
				PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with no file, line and case in score with default prompts');

		$score->getMockController()->getErrors = $allErrors = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
			)
		);

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':', strtoupper($type), $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\''), strtoupper($type), $errorFile, $errorLine, $case))
				) .
				PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with file, no line and no case in score with default prompts');

		$score->getMockController()->getErrors = $allErrors = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
			)
		);

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('Error %s in %s on unknown line, generated by file %s on line %d:', strtoupper($type), $file, $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on unknown line, generated by file %s on line %d'), strtoupper($type), $file, $errorFile, $errorLine))
				) .
				PHP_EOL .
				$message . PHP_EOL
			)
		;

		$this->startCase('There is errors with file, no line and case in score with default prompts');

		$score->getMockController()->getErrors = $allErrors = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid(),
				'errorFile' => $errorFile = uniqid(),
				'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
			)
		);

		$field = new runner\errors\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There is %d error:', sizeof($allErrors)) . PHP_EOL .
				$class . '::' . $method . '():' . PHP_EOL .
				sprintf('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\':', strtoupper($type), $file, $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $errorPrompt = new prompt(uniqid()), $errorColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d error', 'There are %d errors', sizeof($allErrors)), sizeof($allErrors)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize($class . '::' . $method . '()')
				) .
				PHP_EOL .
				$errorPrompt .
				sprintf(
					$locale->_('%s:'),
					$errorColorizer->colorize(sprintf($locale->_('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\''), strtoupper($type), $file, $errorFile, $errorLine, $case))
				) .
				PHP_EOL .
				$message . PHP_EOL
			)
		;
	}
}

?>
