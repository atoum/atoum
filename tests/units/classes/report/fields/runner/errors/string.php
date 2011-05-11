<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\errors;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\errors
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
			->string(runner\errors\string::defaultTitlePrompt)->isEqualTo('> ')
			->string(runner\errors\string::defaultMethodPrompt)->isEqualTo('=> ')
			->string(runner\errors\string::defaultErrorPrompt)->isEqualTo('==> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\errors\string();

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getTitlePrompt())->isEqualTo(runner\errors\string::defaultTitlePrompt)
			->string($field->getMethodPrompt())->isEqualTo(runner\errors\string::defaultMethodPrompt)
			->string($field->getErrorPrompt())->isEqualTo(runner\errors\string::defaultErrorPrompt)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getTitlePrompt())->isEqualTo($titlePrompt)
			->string($field->getMethodPrompt())->isEqualTo($methodPrompt)
			->string($field->getErrorPrompt())->isEqualTo($errorPrompt)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\errors\string();

		$this->assert
			->object($field->setTitlePrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo($prompt)
			->object($field->setTitlePrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getTitlePrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new runner\errors\string();

		$this->assert
			->object($field->setMethodPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo($prompt)
			->object($field->setMethodPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getMethodPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetErrorPrompt()
	{
		$field = new runner\errors\string();

		$this->assert
			->object($field->setErrorPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getErrorPrompt())->isEqualTo($prompt)
			->object($field->setErrorPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getErrorPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\errors\string();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

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
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = function() use ($score) { return $score; };

		$field = new runner\errors\string();

		$this->startCase('There is no error in score with default prompts');

		$score->getMockController()->getErrors = function() { return array(); };

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

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$field = new runner\errors\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d:'), $type, $file, $line, $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d:'), $otherType, $otherFile, $otherLine, $otherErrorFile, $otherErrorLine) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d:'), $type, $file, $line, $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d:'), $otherType, $otherFile, $otherLine, $otherErrorFile, $otherErrorLine) . PHP_EOL .
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

		$field = new runner\errors\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':'), $type, $file, $line, $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':'), $otherType, $otherFile, $otherLine, $otherErrorFile, $otherErrorLine, $otherCase) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':'), $type, $file, $line, $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL .
				$field->getMethodPrompt() . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on line %d, generated by file %s on line %d in case \'%s\':'), $otherType, $otherFile, $otherLine, $otherErrorFile, $otherErrorLine, $otherCase) . PHP_EOL .
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

		$field = new runner\errors\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d:'), $type, $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d:'), $type, $errorFile, $errorLine) . PHP_EOL .
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

		$field = new runner\errors\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $errorFile, $errorLine, $case) . PHP_EOL .
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

		$field = new runner\errors\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d:'), $type, $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d:'), $type, $errorFile, $errorLine) . PHP_EOL .
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

		$field = new runner\errors\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in unknown file on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $errorFile, $errorLine, $case) . PHP_EOL .
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

		$field = new runner\errors\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on unknown line, generated by file %s on line %d:'), $type, $file, $errorFile, $errorLine) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on unknown line, generated by file %s on line %d:'), $type, $file, $errorFile, $errorLine) . PHP_EOL .
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

		$field = new runner\errors\string();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $file, $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$field = new runner\errors\string($locale = new atoum\locale(), $titlePrompt = uniqid(), $methodPrompt = uniqid(), $errorPrompt = uniqid());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . sprintf($field->getLocale()->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				$field->getMethodPrompt() . $class . '::' . $method . '():' . PHP_EOL .
				$field->getErrorPrompt() . sprintf($field->getLocale()->_('Error %s in %s on unknown line, generated by file %s on line %d in case \'%s\':'), $type, $file, $errorFile, $errorLine, $case) . PHP_EOL .
				$message . PHP_EOL
			)
		;
	}
}

?>
