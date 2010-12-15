<?php

namespace mageekguy\atoum\tests\units\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\report;
use \mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../runner.php');

class errors extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(runner\errors::titlePrompt)->isEqualTo('> ')
			->string(runner\errors::methodPrompt)->isEqualTo('=> ')
			->string(runner\errors::errorPrompt)->isEqualTo('==> ')
		;
	}

	public function test__construct()
	{
		$errors = new runner\errors();

		$this->assert
			->object($errors)->isInstanceOf('\mageekguy\atoum\report\fields\runner')
			->variable($errors->getRunner())->isNull()
			->object($errors->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;
	}

	public function testSetWithRunner()
	{
		$errors = new runner\errors();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();

		$this->assert
			->object($errors->setWithRunner($runner))->isIdenticalTo($errors)
			->object($errors->getRunner())->isIdenticalTo($runner)
			->object($errors->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($errors)
			->object($errors->getRunner())->isIdenticalTo($runner)
			->object($errors->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($errors)
			->object($errors->getRunner())->isIdenticalTo($runner)
		;
	}

	public function testToString()
	{
		$errors = new runner\errors($locale = new atoum\locale());

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
			->castToString($errors)->isEmpty()
			->castToString($errors->setWithRunner($runner))->isEmpty()
			->castToString($errors->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($errors->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$allErrors = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid()
			),
			array(
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'file' => $otherFile = uniqid(),
				'line' => $otherLine = rand(1, PHP_INT_MAX),
				'type' => $otherType = uniqid(),
				'message' => ($firstOtherMessage = uniqid()) . PHP_EOL . ($secondOtherMessage = uniqid())
			),
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$errors = new runner\errors($locale = new atoum\locale());

		$this->assert
			->castToString($errors)->isEmpty()
			->castToString($errors->setWithRunner($runner))->isEqualTo(runner\errors::titlePrompt . sprintf($locale->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				runner\errors::methodPrompt . $class . '::' . $method . '():' . PHP_EOL .
				runner\errors::errorPrompt . sprintf($locale->_('Error %s in %s on line %d:'), $type, $file, $line) . PHP_EOL .
				$message . PHP_EOL .
				runner\errors::methodPrompt . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				runner\errors::errorPrompt . sprintf($locale->_('Error %s in %s on line %d:'), $otherType, $otherFile, $otherLine) . PHP_EOL .
				$firstOtherMessage . PHP_EOL .
				$secondOtherMessage . PHP_EOL
			)
		;

		$allErrors = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid()
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$errors = new runner\errors($locale = new atoum\locale());

		$this->assert
			->castToString($errors)->isEmpty()
			->castToString($errors->setWithRunner($runner))->isEqualTo(runner\errors::titlePrompt . sprintf($locale->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				runner\errors::methodPrompt . $class . '::' . $method . '():' . PHP_EOL .
				runner\errors::errorPrompt . sprintf($locale->_('Error %s in unknown file on unknown line:'), $type) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$allErrors = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => null,
				'line' => $line = rand(1, PHP_INT_MAX),
				'type' => $type = uniqid(),
				'message' => $message = uniqid()
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$errors = new runner\errors($locale = new atoum\locale());

		$this->assert
			->castToString($errors)->isEmpty()
			->castToString($errors->setWithRunner($runner))->isEqualTo(runner\errors::titlePrompt . sprintf($locale->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				runner\errors::methodPrompt . $class . '::' . $method . '():' . PHP_EOL .
				runner\errors::errorPrompt . sprintf($locale->_('Error %s in unknown file on line %d:'), $type, $line) . PHP_EOL .
				$message . PHP_EOL
			)
		;

		$allErrors = array(
			array(
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'file' => $file = uniqid(),
				'line' => null,
				'type' => $type = uniqid(),
				'message' => $message = uniqid()
			)
		);

		$score->getMockController()->getErrors = function() use ($allErrors) { return $allErrors; };

		$errors = new runner\errors($locale = new atoum\locale());

		$this->assert
			->castToString($errors)->isEmpty()
			->castToString($errors->setWithRunner($runner))->isEqualTo(runner\errors::titlePrompt . sprintf($locale->__('There is %d error:', 'There are %d errors:', sizeof($allErrors)), sizeof($allErrors)) . PHP_EOL .
				runner\errors::methodPrompt . $class . '::' . $method . '():' . PHP_EOL .
				runner\errors::errorPrompt . sprintf($locale->_('Error %s in %s on unknown line:'), $type, $file) . PHP_EOL .
				$message . PHP_EOL
			)
		;
	}
}

?>
