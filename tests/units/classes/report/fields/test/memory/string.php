<?php

namespace mageekguy\atoum\tests\units\report\fields\test\memory;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\report,
	\mageekguy\atoum\report\fields\test
;

require_once(__DIR__ . '/../../../../../runner.php');

class string extends \mageekguy\atoum\tests\units\report\fields\test\memory
{
	public function testClass()
	{
		$this->assert
			->class($this->getTestedClassName())->isSubClassOf('\mageekguy\atoum\report\fields\test')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(test\memory\string::defaultPrompt)->isEqualTo('=> ')
		;
	}

	public function test__construct()
	{
		$field = new test\memory\string();

		$this->assert
			->object($field->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
			->string($field->getPrompt())->isEqualTo(test\memory\string::defaultPrompt)
			->variable($field->getValue())->isNull()
		;

		$field = new test\memory\string($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->object($field->getLocale())->isIdenticalTo($locale)
			->string($field->getPrompt())->isEqualTo($prompt)
			->variable($field->getValue())->isNull()
		;
	}

	public function testSetPrompt()
	{
		$field = new test\memory\string();

		$this->assert
			->object($field->setPrompt($prompt = uniqid()))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo($prompt)
			->object($field->setPrompt($prompt = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($field)
			->string($field->getPrompt())->isEqualTo((string) $prompt)
		;
	}

	public function testSetWithTest()
	{
		$field = new test\memory\string($locale = new atoum\locale());

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\test')
			->generate('\mageekguy\atoum\score')
		;

		$score = new mock\mageekguy\atoum\score();
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(0, PHP_INT_MAX); };

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
			->integer($field->getValue())->isEqualTo($totalMemoryUsage)
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
		$score->getMockController()->getTotalMemoryUsage = function() use (& $totalMemoryUsage) { return $totalMemoryUsage = rand(0, PHP_INT_MAX); };

		$adapter = new atoum\test\adapter();
		$adapter->class_exists = true;

		$testController = new mock\controller();
		$testController->getTestedClassName = uniqid();

		$test = new mock\mageekguy\atoum\test(null, null, $adapter, $testController);
		$test->getMockController()->getScore = function() use ($score) { return $score; };

		$field = new test\memory\string();

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Memory usage: %4.2f Mb.'), $totalMemoryUsage / 1048576) . PHP_EOL)
		;

		$field = new test\memory\string($locale = new atoum\locale(), $prompt = uniqid());

		$this->assert
			->castToString($field)->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStart))->isEqualTo($field->getPrompt() . $field->getLocale()->_('Memory usage: unknown.') . PHP_EOL)
			->castToString($field->setWithTest($test, atoum\test::runStop))->isEqualTo($field->getPrompt() . sprintf($field->getLocale()->_('Memory usage: %4.2f Mb.'), $totalMemoryUsage / 1048576) . PHP_EOL)
		;
	}
}

?>
