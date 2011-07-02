<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\tests\units,
	\mageekguy\atoum\cli\prompt,
	\mageekguy\atoum\cli\colorizer,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends units\report\fields\runner\duration
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
			->string(runner\duration\cli::defaultPrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\duration\cli();

		$this->assert
			->variable($field->getValue())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($field->getPrompt())->isEqualTo(new prompt(runner\duration\cli::defaultPrompt))
			->object($field->getTitleColorizer())->isEqualTo(new colorizer('1;36'))
			->object($field->getDataColorizer())->isEqualTo(new colorizer())
		;

		$field = new runner\duration\cli(null, null, null, null);

		$this->assert
			->variable($field->getValue())->isNull()
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->object($field->getPrompt())->isEqualTo(new prompt(runner\duration\cli::defaultPrompt))
			->object($field->getTitleColorizer())->isEqualTo(new colorizer('1;36'))
			->object($field->getDataColorizer())->isEqualTo(new colorizer())
		;

		$field = new runner\duration\cli($prompt = new prompt(), $titleColorizer = new colorizer(), $dataColorizer = new colorizer(), $locale = new atoum\locale());

		$this->assert
			->variable($field->getValue())->isNull()
			->object($field->getLocale())->isIdenticalTo($locale)
			->object($field->getPrompt())->isIdenticalTo($prompt)
			->object($field->getTitleColorizer())->isIdenticalTo($titleColorizer)
			->object($field->getDataColorizer())->isIdenticalTo($dataColorizer)
		;
	}

	public function testSetPrompt()
	{
		$field = new runner\duration\cli();

		$this->assert
			->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
			->object($field->getPrompt())->isIdenticalTo($prompt)
		;
	}

	public function testSetTitleColorizer()
	{
		$field = new runner\duration\cli();

		$this->assert
			->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetDataColorizer()
	{
		$field = new runner\duration\cli();

		$this->assert
			->object($field->setDataColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
			->object($field->getDataColorizer())->isIdenticalTo($colorizer)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\duration\cli();

		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getRunningDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

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
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\runner');

		$runner = new mock\mageekguy\atoum\runner();
		$runner->getMockController()->getRunningDuration = $runningDuration = rand(0, PHP_INT_MAX);

		$field = new runner\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('%s: %s.'), $field->getTitleColorizer()->colorize($field->getLocale()->_('Running duration')), $field->getDataColorizer()->colorize($field->getLocale()->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('%s: %s.'), $field->getTitleColorizer()->colorize($field->getLocale()->_('Running duration')), $field->getDataColorizer()->colorize($field->getLocale()->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('%s: %s.'), $field->getTitleColorizer()->colorize($field->getLocale()->_('Running duration')), $field->getDataColorizer()->colorize($field->getLocale()->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('%s: %s.'), $field->getTitleColorizer()->colorize($field->getLocale()->_('Running duration')), $field->getDataColorizer()->colorize(sprintf($field->getLocale()->__('%4.2f second', '%4.2f seconds', $runningDuration), $runningDuration))) . PHP_EOL)
		;

		$field = new runner\duration\cli($prompt = new prompt(), $titleColorizer = new colorizer(uniqid(), uniqid()), $dataColorizer = new colorizer(uniqid(), uniqid()), $locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . sprintf($locale->_('%s: %s.'), $titleColorizer->colorize($locale->_('Running duration')), $dataColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . sprintf($locale->_('%s: %s.'), $titleColorizer->colorize($locale->_('Running duration')), $dataColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . sprintf($locale->_('%s: %s.'), $titleColorizer->colorize($locale->_('Running duration')), $dataColorizer->colorize($locale->_('unknown'))) . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($locale->_('%s: %s.'), $titleColorizer->colorize($locale->_('Running duration')), $dataColorizer->colorize(sprintf($locale->__('%4.2f second', '%4.2f seconds', $runningDuration), $runningDuration))) . PHP_EOL)
		;
	}
}

?>
