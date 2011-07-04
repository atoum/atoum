<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\locale,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner\duration,
	\mageekguy\atoum\tests\units\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends runner\duration
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubclassOf('\mageekguy\atoum\report\fields\runner')
		;
	}

	public function test__construct()
	{
		$field = new duration\cli();

		$this->assert
			->variable($field->getPrompt())->isNull()
			->variable($field->getTitleColorizer())->isNull()
			->variable($field->getDurationColorizer())->isNull()
			->variable($field->getValue())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;

		$field = new duration\cli(null, null, null, null);

		$this->assert
			->variable($field->getPrompt())->isNull()
			->variable($field->getTitleColorizer())->isNull()
			->variable($field->getDurationColorizer())->isNull()
			->variable($field->getValue())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;

		$field = new duration\cli($prompt = new prompt(), $titleColorizer = new colorizer(), $durationColorizer = new colorizer(), $locale = new locale());

		$this->assert
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getDurationColorizer())->isIdenticalTo($durationColorizer)
			->variable($field->getValue())->isNull()
			->object($field->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetPrompt()
	{
		$field = new duration\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new duration\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetDurationColorizer()
	{
		$field = new duration\cli();

		$this->assert
			->object($field->setDurationColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getDurationColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new duration\cli();

		$this->mock
			->generate('\mageekguy\atoum\runner')
		;

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getRunningDuration = $runningDuration = rand(0, PHP_INT_MAX);

		$this->assert
			->variable($field->getValue())->isNull()
			->object($field->setWithRunner($runner))->isIdenticalTo($field)
			->variable($field->getValue())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStart))->isIdenticalTo($field)
			->variable($field->getValue())->isNull()
			->object($field->setWithRunner($runner, atoum\runner::runStop))->isIdenticalTo($field)
			->integer($field->getValue())->isEqualTo($runningDuration)
		;
	}

	public function test__toString()
	{
		$this->mock
			->generate('\mageekguy\atoum\runner')
		;

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getRunningDuration = $runningDuration = rand(0, PHP_INT_MAX);

		$field = new duration\cli();

		$this->assert
			->castToString($field)->isEqualTo(sprintf($field->getLocale()->_('%s: %s.'), $field->getLocale()->_('Running duration'), $field->getLocale()->_('unknown')) . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo(sprintf($field->getLocale()->_('%s: %s.'), $field->getLocale()->_('Running duration'), $field->getLocale()->_('unknown')) . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo(sprintf($field->getLocale()->_('%s: %s.'), $field->getLocale()->_('Running duration'), $field->getLocale()->_('unknown')) . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo(sprintf($field->getLocale()->_('%s: %s.'), $field->getLocale()->_('Running duration'), sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $runningDuration), $runningDuration)) . PHP_EOL)
		;

		$field = new duration\cli($prompt = new prompt(), $titleColorizer = new colorizer(uniqid(), uniqid()), $durationColorizer = new colorizer(uniqid(), uniqid()), $locale = new locale());

		$this->assert
			->castToString($field)->isEqualTo($prompt . sprintf($locale->_('%s: %s.'), $titleColorizer->colorize($locale->_('Running duration')), $durationColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($prompt . sprintf($locale->_('%s: %s.'), $titleColorizer->colorize($locale->_('Running duration')), $durationColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($prompt . sprintf($locale->_('%s: %s.'), $titleColorizer->colorize($locale->_('Running duration')), $durationColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($prompt . sprintf($locale->_('%s: %s.'), $titleColorizer->colorize($locale->_('Running duration')), $durationColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $runningDuration), $runningDuration))) . PHP_EOL)
		;
	}
}

?>
