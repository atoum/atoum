<?php

namespace mageekguy\atoum\tests\units\report\fields\test\duration;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\test
;

require_once(__DIR__ . '/../../../../../runner.php');

class cli extends \mageekguy\atoum\tests\units\report\fields\test\duration
{
	public function testClass()
	{
		$this->assert
			->class('\mageekguy\atoum\report\fields\test\duration\cli')->isSubClassOf('\mageekguy\atoum\report\fields\test')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(test\duration\cli::defaultPrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$field = new test\duration\cli();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->variable($field->getValue())->isNull()
			->string($field->getPrompt())->isEqualTo(test\duration\cli::defaultPrompt)
		;

		$field = new test\duration\cli($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->variable($field->getValue())->isNull()
			->string($field->getPrompt())->isEqualTo($prompt)
		;
	}

	public function testSetWithTest()
	{
		$field = new test\duration\cli($locale = new atoum\locale());

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
			->object($field->setPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo($prompt)
			->object($field->setPrompt($prompt = rand(1, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo((string) $prompt)
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

		$field = new test\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;

		$field = new test\duration\cli($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;

		$score->getMockController()->getTotalDuration = $runningDuration = rand(1, 1000) / 1000;

		$field = new test\duration\cli();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;

		$field = new test\duration\cli($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Test duration: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->__('Test duration: %4.2f second.', 'Test duration: %4.2f seconds.', $runningDuration), $runningDuration) . PHP_EOL)
		;
	}
}

?>
