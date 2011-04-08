<?php

namespace mageekguy\atoum\tests\units\report\fields\test\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\test
;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\test\duration
{
	public function testClass()
	{
		$this->assert
			->class('\mageekguy\atoum\report\fields\test\duration\string')->isSubClassOf('\mageekguy\atoum\report\fields\test')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(test\duration\string::titlePrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$duration = new test\duration\string();

		$this->assert
			->object($duration->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($duration->getValue())->isNull()
			->string($duration->getSingularLabel())->isEqualTo('Test duration: %4.2f second.')
			->string($duration->getPluralLabel())->isEqualTo('Test duration: %4.2f seconds.')
			->string($duration->getPrompt())->isEqualTo(test\duration\string::titlePrompt)
		;

		$duration = new test\duration\string($locale = new atoum\locale(), $singularLabel = uniqid(), $pluralLabel = uniqid(), $prompt = uniqid());

		$this->assert
			->object($duration->getLocale())->isIdenticalTo($locale)
			->variable($duration->getValue())->isNull()
			->string($duration->getSingularLabel())->isEqualTo($singularLabel)
			->string($duration->getPluralLabel())->isEqualTo($pluralLabel)
			->string($duration->getPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetWithTest()
	{
		$duration = new test\duration\string($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = function() use (& $runningDuration) { return $runningDuration = rand(0, PHP_INT_MAX); };

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$this->assert
			->variable($duration->getValue())->isNull()
			->object($duration->setWithTest($test))->isIdenticalTo($duration)
			->variable($duration->getValue())->isNull()
			->object($duration->setWithTest($test, atoum\test::runStart))->isIdenticalTo($duration)
			->variable($duration->getValue())->isNull()
			->object($duration->setWithTest($test, atoum\test::runStop))->isIdenticalTo($duration)
			->integer($duration->getValue())->isEqualTo($runningDuration)
		;
	}

	public function testSetPrompt()
	{
		$duration = new test\duration\string();

		$this->assert
			->object($duration->setPrompt($prompt = uniqid()))->isIdenticalTo($duration)
			->string($duration->getPrompt())->isEqualTo($prompt)
			->object($duration->setPrompt($prompt = rand(1, PHP_INT_MAX)))->isIdenticalTo($duration)
			->string($duration->getPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetSingularLabel()
	{
		$duration = new test\duration\string();

		$this->assert
			->object($duration->setSingularLabel($label = uniqid()))->isIdenticalTo($duration)
			->string($duration->getSingularLabel())->isEqualTo($label)
			->object($duration->setSingularLabel($label = rand(1, PHP_INT_MAX)))->isIdenticalTo($duration)
			->string($duration->getSingularLabel())->isEqualTo((string) $label)
		;
	}

	public function testSetPluralLabel()
	{
		$duration = new test\duration\string();

		$this->assert
			->object($duration->setPluralLabel($label = uniqid()))->isIdenticalTo($duration)
			->string($duration->getPluralLabel())->isEqualTo($label)
			->object($duration->setPluralLabel($label = rand(1, PHP_INT_MAX)))->isIdenticalTo($duration)
			->string($duration->getPluralLabel())->isEqualTo((string) $label)
		;
	}

	public function test__toString()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalDuration = $runningDuration = rand(2, PHP_INT_MAX);

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$duration = new test\duration\string($locale = new atoum\locale());

		$this->assert
			->castToString($duration)->isEqualTo(test\duration\string::titlePrompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test))->isEqualTo(test\duration\string::titlePrompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test, atoum\test::runStart))->isEqualTo(test\duration\string::titlePrompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test, atoum\test::runStop))->isEqualTo(test\duration\string::titlePrompt . sprintf($locale->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;

		$duration = new test\duration\string($locale = new atoum\locale(), $singularLabel = uniqid(), $pluralLabel = uniqid(), $prompt = uniqid());

		$this->assert
			->castToString($duration)->isEqualTo($prompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test))->isEqualTo($prompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test, atoum\test::runStart))->isEqualTo($prompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test, atoum\test::runStop))->isEqualTo($prompt . $pluralLabel . PHP_EOL)
		;

		$score->getMockController()->getTotalDuration = $runningDuration = rand(1, 1000) / 1000;

		$duration = new test\duration\string($locale = new atoum\locale(), $singularLabel = uniqid(), $pluralLabel = uniqid(), $prompt = uniqid());

		$this->assert
			->castToString($duration)->isEqualTo($prompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test))->isEqualTo($prompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test, atoum\test::runStart))->isEqualTo($prompt . $locale->_('Test duration: unknown.') . PHP_EOL)
			->castToString($duration->setWithTest($test, atoum\test::runStop))->isEqualTo($prompt . $singularLabel . PHP_EOL)
		;
	}
}

?>
