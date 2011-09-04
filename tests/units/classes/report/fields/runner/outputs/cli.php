<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\outputs;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\tests\units,
	mageekguy\atoum\report\fields\runner\outputs
;

require_once __DIR__ . '/../../../../../runner.php';

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
		$field = new outputs\cli();

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getMethodColorizer())->isEqualTo(new colorizer())
			->object($field->getOutputPrompt())->isEqualTo(new prompt())
			->object($field->getOutputColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getRunner())->isNull()
		;

		$field = new outputs\cli(null, null, null, null, null, null, null);

		$this->assert
			->object($field->getTitlePrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMethodPrompt())->isEqualTo(new prompt())
			->object($field->getMethodColorizer())->isEqualTo(new colorizer())
			->object($field->getOutputPrompt())->isEqualTo(new prompt())
			->object($field->getOutputColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getRunner())->isNull()
		;

		$field = new outputs\cli($titlePrompt = new prompt(), $titleColorizer = new colorizer(), $methodPrompt = new prompt(), $methodColorizer = new colorizer(), $outputPrompt = new prompt(), $outputColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getTitlePrompt())->isIdenticalTo($titlePrompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getMethodPrompt())->isIdenticalTo($methodPrompt)
			->object($field->getMethodColorizer())->isIdenticalTo($methodColorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getRunner())->isNull()
		;
	}

	public function testSetTitlePrompt()
	{
		$field = new outputs\cli();

		$this->assert
			->object($field->setTitlePrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getTitlePrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new outputs\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetMethodPrompt()
	{
		$field = new outputs\cli();

		$this->assert
			->object($field->setMethodPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getMethodPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetMethodColorizer()
	{
		$field = new outputs\cli();

		$this->assert
			->object($field->setMethodColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getMethodColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetOutputPrompt()
	{
		$field = new outputs\cli();

		$this->assert
			->object($field->setOutputPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getOutputPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetOutputColorizer()
	{
		$field = new outputs\cli();

		$this->assert
			->object($field->setOutputColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getOutputColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new outputs\cli();

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
		$field = new outputs\cli();

		$this->mockGenerator
			->generate('mageekguy\atoum\score')
			->generate('mageekguy\atoum\runner')
		;

		$score = new \mock\mageekguy\atoum\score();
		$score->getMockController()->getOutputs = array();

		$runner = new \mock\mageekguy\atoum\runner();
		$runner->getMockController()->getScore = $score;

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEmpty()
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEmpty()
		;

		$score->getMockController()->getOutputs = $fields = array(
			array(
				'case' => null,
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'value' => $value = uniqid()
			),
			array(
				'case' => null,
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'value' => ($firstOtherValue = uniqid()) . PHP_EOL . ($secondOtherValue = uniqid())
			)
		);

		$field = new outputs\cli();

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf('There are %d outputs:', sizeof($fields)) . PHP_EOL .
				'In ' . $class . '::' . $method . '():' . PHP_EOL .
				$value . PHP_EOL .
				'In ' . $otherClass . '::' . $otherMethod . '():' . PHP_EOL .
				$firstOtherValue . PHP_EOL .
				$secondOtherValue . PHP_EOL
			)
		;

		$field = new outputs\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d output', 'There are %d outputs', sizeof($fields)), sizeof($fields)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize('In ' . $class . '::' . $method . '()')
				) .
				PHP_EOL .
				$outputPrompt .
				$outputColorizer->colorize($value) . PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize('In ' . $otherClass . '::' . $otherMethod . '()')
				) .
				PHP_EOL .
				$outputPrompt . $outputColorizer->colorize($firstOtherValue) . PHP_EOL .
				$outputPrompt . $outputColorizer->colorize($secondOtherValue) . PHP_EOL
			)
		;

		$score->getMockController()->getOutputs = $fields = array(
			array(
				'case' => $case = uniqid(),
				'class' => $class = uniqid(),
				'method' => $method = uniqid(),
				'value' => $value = uniqid()
			),
			array(
				'case' => $otherCase = uniqid(),
				'class' => $otherClass = uniqid(),
				'method' => $otherMethod = uniqid(),
				'value' => ($firstOtherValue = uniqid()) . PHP_EOL . ($secondOtherValue = uniqid())
			),
		);

		$field = new outputs\cli($titlePrompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $methodPrompt = new prompt(uniqid()), $methodColorizer = new colorizer(uniqid(), uniqid()), $outputPrompt = new prompt(uniqid()), $outputColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEmpty()
			->castToString($field->setWithRunner($runner))->isEqualTo(
				$titlePrompt .
				sprintf(
					$locale->_('%s:'),
					$titleColorizer->colorize(sprintf($locale->__('There is %d output', 'There are %d outputs', sizeof($fields)), sizeof($fields)))
				) .
				PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize('In ' . $class . '::' . $method . '() in case \'' . $case . '\'')
				) .
				PHP_EOL .
				$outputPrompt .
				$outputColorizer->colorize($value) . PHP_EOL .
				$methodPrompt .
				sprintf(
					$locale->_('%s:'),
					$methodColorizer->colorize('In ' . $otherClass . '::' . $otherMethod . '() in case \'' . $otherCase . '\'')
				) .
				PHP_EOL .
				$outputPrompt . $outputColorizer->colorize($firstOtherValue) . PHP_EOL .
				$outputPrompt . $outputColorizer->colorize($secondOtherValue) . PHP_EOL
			)
		;
	}
}

?>
