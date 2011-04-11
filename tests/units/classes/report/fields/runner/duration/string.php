<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\runner,
	\mageekguy\atoum\tests\units\report\fields
;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\runner\duration
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
			->string(runner\duration\string::defaultPrompt)->isEqualTo('> ')
		;
	}

	public function test__construct()
	{
		$field = new runner\duration\string();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getValue())->isNull()
			->string($field->getPrompt())->isEqualTo(runner\duration\string::defaultPrompt)
		;

		$field = new runner\duration\string($locale = new atoum\locale(), $prompt = uniqid(), $singularLabel = uniqid(), $pluralLabel = uniqid(), $unknownLabel = uniqid());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getValue())->isNull()
			->string($field->getPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetPrompt()
	{
		$field = new runner\duration\string();

		$this->assert
			->object($field->setPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo($prompt)
			->object($field->setPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithRunner()
	{
		$field = new runner\duration\string($locale = new atoum\locale());

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
		$runner->getMockController()->getRunningDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$field = new runner\duration\string($locale = new atoum\locale());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($field->getPrompt() . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($field->getPrompt() . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($field->getPrompt() . sprintf($locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;

		$field = new runner\duration\string($locale = new atoum\locale(), $prompt = uniqid(), $singularLabel = uniqid(), $pluralLabel = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($prompt . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner))->isEqualTo($prompt . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStart))->isEqualTo($prompt . $locale->_('Running duration: unknown.') . PHP_EOL)
			->castToString($field->setWithRunner($runner, atoum\runner::runStop))->isEqualTo($prompt . sprintf($locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;
	}
}

?>
