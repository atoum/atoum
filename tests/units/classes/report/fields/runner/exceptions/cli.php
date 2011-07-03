<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\exceptions;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\runner\exceptions
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
			->string(runner\exceptions\cli::defaultTitlePrompt)->isEqualTo('> ')
			->string(runner\exceptions\cli::defaultMethodPrompt)->isEqualTo('=> ')
			->string(runner\exceptions\cli::defaultExceptionPrompt)->isEqualTo('==> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\exceptions\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt(runner\exceptions\cli::defaultTitlePrompt))
			->object($field->getTitleColorizer())->isEqualTo(new colorizer('0;35'))
			->object($field->getMethodPrompt())->isEqualTo(new prompt(runner\exceptions\cli::defaultMethodPrompt, new colorizer('0;35')))
			->object($field->getMethodColorizer())->isEqualTo(new colorizer('0;35'))
			->object($field->getExceptionPrompt())->isEqualTo(new prompt(runner\exceptions\cli::defaultExceptionPrompt, new colorizer('0;35')))
			->object($field->getExceptionColorizer())->isEqualTo(new colorizer('0;35'))
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;

		$field = new runner\exceptions\cli(null, null, null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt(runner\exceptions\cli::defaultTitlePrompt))
			->object($field->getTitleColorizer())->isEqualTo(new colorizer('0;35'))
			->object($field->getMethodPrompt())->isEqualTo(new prompt(runner\exceptions\cli::defaultMethodPrompt, new colorizer('0;35')))
			->object($field->getMethodColorizer())->isEqualTo(new colorizer('0;35'))
			->object($field->getExceptionPrompt())->isEqualTo(new prompt(runner\exceptions\cli::defaultExceptionPrompt, new colorizer('0;35')))
			->object($field->getExceptionColorizer())->isEqualTo(new colorizer('0;35'))
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;

		$field = new runner\exceptions\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(), $exceptionPrompt = new prompt(uniqid()), $exceptionColorizer = new colorizer(), $locale = new atoum\locale());

		$this->assert
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
			->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
			->object($field->getExceptionPrompt())->isIdenticalTo($exceptionPrompt)
			->object($field->getExceptionColorizer())->isIdenticalTo($exceptionColorizer)
			->variable($field->getRunner())->isNull()
			->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new runner\exceptions\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new runner\exceptions\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new runner\exceptions\cli();

		$this->assert
			->object($field->setMethodPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getMethodPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetMethodColorizer()
	{
		$field = new runner\exceptions\cli();

		$this->assert
			->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetExceptionPrompt()
	{
		$field = new runner\exceptions\cli();

		$this->assert
			->object($field->setExceptionPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getExceptionPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetExceptionColorizer()
	{
		$field = new runner\exceptions\cli();

		$this->assert
			->object($field->setExceptionColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getExceptionColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\exceptions\cli();

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
		$this->mock
			->generate('\mageekguy\atoum\score')
			->generate('\mageekguy\atoum\runner')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getExceptions = array();

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$field = new runner\exceptions\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$score->getMockController()->getExceptions = $exceptions = array(
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

		$field = new runner\exceptions\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $field->getTitleColorizer()->colorize(sprintf($field->getLocale()->__('There is %d exception:', 'There are %d exceptions:', sizeof($exceptions)), sizeof($exceptions))) . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getExceptionPrompt() . $field->getExceptionColorizer()->colorize(sprintf($field->getLocale()->_('Exception throwed in file %s on line %d:'), $file, $line)) . PHP_EOL .
				$value . PHP_EOL .
				$field->getMethodPrompt() . $field->getMethodColorizer()->colorize($otherClass . '::' . $otherMethod . '():') . PHP_EOL .
				$field->getExceptionPrompt() . $field->getExceptionColorizer()->colorize(sprintf($field->getLocale()->_('Exception throwed in file %s on line %d:'), $otherFile, $otherLine)) . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;

		$field = new runner\exceptions\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $exceptionPrompt = new prompt(uniqid()), $exceptionColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getTitlePrompt() . $titleColorizer->colorize(sprintf($field->getLocale()->__('There is %d exception:', 'There are %d exceptions:', sizeof($exceptions)), sizeof($exceptions))) . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($class . '::' . $method . '():') . PHP_EOL .
				$field->getExceptionPrompt() . $exceptionColorizer->colorize(sprintf($locale->_('Exception throwed in file %s on line %d:'), $file, $line)) . PHP_EOL .
				$value . PHP_EOL .
				$field->getMethodPrompt() . $methodColorizer->colorize($otherClass . '::' . $otherMethod . '():') . PHP_EOL .
				$field->getExceptionPrompt() . $exceptionColorizer->colorize(sprintf($locale->_('Exception throwed in file %s on line %d:'), $otherFile, $otherLine)) . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;
	}
}

?>
