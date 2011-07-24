<?php

namespace mageekguy\atoum\tests\units\report\fields\test\duration;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\test\adapter,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\tests\units
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\test\duration
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubClassOf('mageekguy\atoum\report\fields\test')
		;
	}

	public function test__construct()
	{
		$field = new test\duration\cli();

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getDurationColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getValue())->isNull()
		;

		$field = new test\duration\cli(null, null, null, null);

		$this->assert
			->object($field->getPrompt())->isEqualTo(new prompt())
			->object($field->getTitleColorizer())->isEqualTo(new colorizer())
			->object($field->getDurationColorizer())->isEqualTo(new colorizer())
			->object($field->getLocale())->isEqualTo(new locale())
			->variable($field->getValue())->isNull()
		;

		$field = new test\duration\cli($prompt = new prompt(), $titleColorizer = new colorizer(), $durationColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getDurationColorizer())->isIdenticalTo($durationColorizer)
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getValue())->isNull()
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new test\duration\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetDurationColorizer()
	{
		$field = new test\duration\cli();

		$this->assert
			->object($field->setDurationColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getDurationColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithTest()
	{
		$field = new test\duration\cli();

		$this->mockGenerator
			->generate('mageekguy\atoum\test')
			->generate('mageekguy\atoum\score')
		;

		$score = new \mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$adapter = new adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();
		$testController->getScore = $score;

		$test = new \mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$this->assert
			->variable($field->getValue())->isNull()
			->object($field->setWithTest($test))->isIdenticalTo($field)
			->variable($field->getValue())->isNull()
			->object($field->setWithTest($test, atoum\test::runStart))->isIdenticalTo($field)
			->variable($field->getValue())->isNull()
			->object($field->setWithTest($test, atoum\test::runStop))->isIdenticalTo($field)
			->integer($field->getValue())->isEqualTo($runningDuration)
		;
	}

	public function testSetPrompt()
	{
		$field = new test\duration\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt()))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function test__toString()
	{
		$adapter = new adapter();
		$adapter->class_exists = true;

		$this->mockGenerator
			->generate('mageekguy\atoum\test')
			->generate('mageekguy\atoum\score')
		;

		$score = new \mock\mageekguy\atoum\score();

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();
		$testController->getScore = $score;

		$test = new \mock\mageekguy\atoum\test(null, null, $adapter, $testController);

		$field = new test\duration\cli();

		$score->getMockController()->getTotalDuration = $runningDuration = rand(1, 1000) / 1000;

		$this->assert
			->castToString($field)->isEqualTo('Test duration: unknown.' . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo('Test duration: unknown.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo('Test duration: unknown.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo(sprintf('Test duration: %4.2f second.', $runningDuration) . PHP_EOL)
		;

		$field = new test\duration\cli($prompt = new prompt(), $titleColorizer = new colorizer(), $durationColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo(
					$prompt .
					sprintf(
						'%1$s: %2$s.',
						$titleColorizer->colorize($locale->_('Test duration')),
						$locale->_('unknown')
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test))->isEqualTo(
					$prompt .
					sprintf(
						'%1$s: %2$s.',
						$titleColorizer->colorize($locale->_('Test duration')),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo(
					$prompt .
					sprintf(
						'%1$s: %2$s.',
						$titleColorizer->colorize($locale->_('Test duration')),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo(
					$prompt .
					sprintf(
						'%1$s: %2$s.',
						$titleColorizer->colorize($locale->_('Test duration')),
						$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $runningDuration), $runningDuration))
					) .
					PHP_EOL
				)
		;

		$score->getMockController()->getTotalDuration = $runningDuration = rand(2, PHP_INT_MAX);

		$field = new test\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo('Test duration: unknown.' . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo('Test duration: unknown.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo('Test duration: unknown.' . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo(sprintf('Test duration: %4.2f seconds.', $runningDuration) . PHP_EOL)
		;

		$field = new test\duration\cli($prompt = new prompt(uniqid()), $titleColorizer = new colorizer(uniqid(), uniqid()), $durationColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo(
					$prompt .
					sprintf(
						$locale->_('%1$s: %2$s.'),
						$titleColorizer->colorize($locale->_('Test duration')),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test))->isEqualTo(
					$prompt .
					sprintf(
						$locale->_('%1$s: %2$s.'),
						$titleColorizer->colorize($locale->_('Test duration')),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo(
					$prompt .
					sprintf(
						$locale->_('%1$s: %2$s.'),
						$titleColorizer->colorize($locale->_('Test duration')),
						$durationColorizer->colorize($locale->_('unknown'))
					) .
					PHP_EOL
				)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo(
					$prompt .
					sprintf(
						$locale->_('%1$s: %2$s.'),
						$titleColorizer->colorize($locale->_('Test duration')),
						$durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $runningDuration), $runningDuration))
					) .
					PHP_EOL
				)
		;
	}
}

?>
