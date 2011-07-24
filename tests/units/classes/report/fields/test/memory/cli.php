<?php

namespace mageekguy\atoum\tests\units\report\fields\test\memory;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\tests\units
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\test\memory
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubClassOf('mageekguy\atoum\report\fields\test')
		;
	}

	public function test__construct()
	{
		$field = new test\memory\cli();

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getValue())->isNull()
		;

		$field = new test\memory\cli(null, null, null, null);

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getMemoryColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getValue())->isNull()
		;

		$field = new test\memory\cli($prompt = new prompt(), $titleColorizer = new colorizer(), $memoryColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getMemoryColorizer())->isIdenticalTo($memoryColorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getValue())->isNull()
		;
	}

	public function testSetPrompt()
	{
		$field = new test\memory\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new test\memory\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetDurationColorizer()
	{
		$field = new test\memory\cli();

		$this->assert
			->object($field->setMemoryColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getMemoryColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithTest()
	{
		$field = new test\memory\cli();

		$this->mock
			->generate('mageekguy\atoum\test')
			->generate('mageekguy\atoum\score')
		;

		$score = new \mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = $totalMemoryUsage = rand(0, PHP_INT_MAX);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new atoum\mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new \mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$test->getMockController()->getScore = $score;

		$this->assert
			->variable($field->getValue())->isNull()
			->object($field->setWithTest($test))->isIdenticalTo($field)
			->variable($field->getValue())->isNull()
			->object($field->setWithTest($test, atoum\test::runStart))->isIdenticalTo($field)
			->variable($field->getValue())->isNull()
			->object($field->setWithTest($test, atoum\test::runStop))->isIdenticalTo($field)
			->integer($field->getValue())->isEqualTo($totalMemoryUsage)
		;
	}

	public function test__toString()
	{
		$this->mock
			->generate('mageekguy\atoum\test')
			->generate('mageekguy\atoum\score')
		;

		$score = new \mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = $totalMemoryUsage = rand(0, PHP_INT_MAX);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new atoum\mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new \mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$test->getMockController()->getScore = $score;

		$field = new test\memory\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Memory usage: %4.2f Mb.'), $totalMemoryUsage / 1048576) . PHP_EOL)
		;

		$field = new test\memory\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $memoryColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo(
					$prompt .
					sprintf(
						$locale->_('%1$s: %2$s.'),
						$titleColorizer->colorize($locale->_('Memory usage')),
						$memoryColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test))->isEqualTo(
					$prompt .
					sprintf(
						$locale->_('%1$s: %2$s.'),
						$titleColorizer->colorize($locale->_('Memory usage')),
						$memoryColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo(
					$prompt .
					sprintf(
						$locale->_('%1$s: %2$s.'),
						$titleColorizer->colorize($locale->_('Memory usage')),
						$memoryColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo(
					$prompt .
					sprintf(
						$locale->_('%1$s: %2$s.'),
						$titleColorizer->colorize($locale->_('Memory usage')),
						$memoryColorizer->colorize(sprintf($locale->_('%4.2f Mb'), $totalMemoryUsage / 1048576))
					) .
					PHP_EOL
				)
		;
	}
}

?>
