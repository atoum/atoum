<?php

namespace mageekguy\atoum\tests\units\report\fields\test\event;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\report\fields\test\event\tap as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class tap extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\event');
	}

	public function test__construct()
	{
		$this
			->if($field = new testedClass())
			->then
				->variable($field->getObservable())->isNull()
				->variable($field->getEvent())->isNull()
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->class_exists = true)
			->and($runner = new \mock\mageekguy\atoum\runner())
			->and($testController = new mock\controller())
			->and($testController->getTestedClassName = uniqid())
			->and($test = new \mock\mageekguy\atoum\test($adapter))
			->and($field = new testedClass())
			->then
				->boolean($field->handleEvent(atoum\runner::runStart, $runner))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\runner::runStart)
				->object($field->getObservable())->isIdenticalTo($runner)
				->boolean($field->handleEvent(atoum\runner::runStop, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::runStart, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeSetUp, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::afterSetUp, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeTestMethod, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::fail, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::fail)
				->object($field->getObservable())->isIdenticalTo($test)
				->boolean($field->handleEvent(atoum\test::error, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::error)
				->object($field->getObservable())->isIdenticalTo($test)
				->boolean($field->handleEvent(atoum\test::exception, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::exception)
				->object($field->getObservable())->isIdenticalTo($test)
				->boolean($field->handleEvent(atoum\test::success, $test))->isTrue()
				->string($field->getEvent())->isEqualTo(atoum\test::success)
				->object($field->getObservable())->isIdenticalTo($test)
				->boolean($field->handleEvent(atoum\test::afterTestMethod, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::beforeTearDown, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::afterTearDown, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
				->boolean($field->handleEvent(atoum\test::runStop, $test))->isFalse()
				->variable($field->getEvent())->isNull()
				->variable($field->getObservable())->isNull()
		;
	}

	public function test__toStringWithFailures()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($score = new \mock\atoum\test\score())
			->and($this->calling($score)->getLastFailAssertion[1] = $failure1 = array(
					'case' => null,
					'dataSetKey' => null,
					'class' => $class1 = uniqid(),
					'method' => $method1 = uniqid(),
					'file' => $file1 = uniqid(),
					'line' => $line1 = uniqid(),
					'asserter' => $asserter1 = uniqid(),
					'fail' => $fail1 = uniqid()
				)
			)
			->and($this->calling($score)->getLastFailAssertion[2] = $failure2 = array(
					'case' => $case2 = uniqid(),
					'dataSetKey' => null,
					'class' => $class2 = uniqid(),
					'method' => $method2 = uniqid(),
					'file' => $file2 = uniqid(),
					'line' => $line2 = uniqid(),
					'asserter' => $asserter2 = uniqid(),
					'fail' => $fail2 = uniqid()
				)
			)
			->and($this->calling($score)->getLastFailAssertion[3] = $failure3 = array(
					'case' => null,
					'dataSetKey' => null,
					'class' => $class3 = uniqid(),
					'method' => $method3 = uniqid(),
					'file' => $file3 = uniqid(),
					'line' => $line3 = uniqid(),
					'asserter' => $asserter3 = uniqid(),
					'fail' => ($fail3 = uniqid()) . PHP_EOL . ($otherFail3 = uniqid()) . PHP_EOL . ($anotherFail3 = uniqid()) . PHP_EOL
				)
			)
			->and($this->calling($score)->getLastFailAssertion[4] = $failure4 = array(
					'case' => null,
					'dataSetKey' => null,
					'class' => $class4 = uniqid(),
					'method' => $method4 = uniqid(),
					'file' => $file4 = uniqid(),
					'line' => $line4 = uniqid(),
					'asserter' => $asserter4 = uniqid(),
					'fail' => $fail4 = uniqid()
				)
			)
			->and($test = new \mock\mageekguy\atoum\test())
			->and($this->calling($test)->getScore = $score)
			->and($field = new testedClass())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('not ok 1 - ' . $class1 . '::' . $method1 . '()' . PHP_EOL . '# ' . $fail1 . PHP_EOL . '# ' . $file1 . ':' . $line1 . PHP_EOL)
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 - ' . $class2 . '::' . $method2 . '()' . PHP_EOL . '# ' . $fail2 . PHP_EOL . '# ' . $file2 . ':' . $line2 . PHP_EOL)
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('not ok 3 - ' . $class3 . '::' . $method3 . '()' . PHP_EOL . '# ' . $fail3 . PHP_EOL . '# ' . $otherFail3 . PHP_EOL . '# ' . $anotherFail3 . PHP_EOL . '# ' . $file3 . ':' . $line3 . PHP_EOL)
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('not ok 4 - ' . $class4 . '::' . $method4 . '()' . PHP_EOL . '# ' . $fail4 . PHP_EOL . '# ' . $file4 . ':' . $line4 . PHP_EOL)
			->if($score->getMockController()->resetCalls())
			->and($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('not ok 1 - ' . $class1 . '::' . $method1 . '()' . PHP_EOL . '# ' . $fail1 . PHP_EOL . '# ' . $file1 . ':' . $line1 . PHP_EOL)
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 - ' . $class2 . '::' . $method2 . '()' . PHP_EOL . '# ' . $fail2 . PHP_EOL . '# ' . $file2 . ':' . $line2 . PHP_EOL)
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('not ok 3 - ' . $class3 . '::' . $method3 . '()' . PHP_EOL . '# ' . $fail3 . PHP_EOL . '# ' . $otherFail3 . PHP_EOL . '# ' . $anotherFail3 . PHP_EOL . '# ' . $file3 . ':' . $line3 . PHP_EOL)
			->if($field->handleEvent(atoum\test::fail, $test))
			->then
				->castToString($field)->isEqualTo('not ok 4 - ' . $class4 . '::' . $method4 . '()' . PHP_EOL . '# ' . $fail4 . PHP_EOL . '# ' . $file4 . ':' . $line4 . PHP_EOL)
		;
	}

	public function test__toStringWithVoid()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($score = new \mock\atoum\test\score())
			->and($this->calling($score)->getLastVoidMethod[1] = array(
					'class' => $class1 = uniqid(),
					'method' => $method1 = uniqid(),
                    'file' => $file1 = uniqid(),
					'line' => $line1 = uniqid(),
				)
			)
			->and($this->calling($score)->getLastVoidMethod[2] = array(
					'class' => $class2 = uniqid(),
					'method' => $method2 = uniqid(),
                    'file' => $file2 = uniqid(),
					'line' => $line2 = uniqid(),
				)
			)
			->and($test = new \mock\mageekguy\atoum\test())
			->and($this->calling($test)->getScore = $score)
			->and($field = new testedClass())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::void, $test))
			->then
				->castToString($field)->isEqualTo('not ok 1 # TODO ' . $class1 . '::' . $method1 . '()' . PHP_EOL . '# ' . $file1 . ':' . $line1 . PHP_EOL)
			->if($field->handleEvent(atoum\test::void, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 # TODO ' . $class2 . '::' . $method2 . '()' . PHP_EOL . '# ' . $file2 . ':' . $line2 . PHP_EOL)
			->if($score->getMockController()->resetCalls())
			->and($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::void, $test))
			->then
				->castToString($field)->isEqualTo('not ok 1 # TODO ' . $class1 . '::' . $method1 . '()' . PHP_EOL . '# ' . $file1 . ':' . $line1 . PHP_EOL)
			->if($field->handleEvent(atoum\test::void, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 # TODO ' . $class2 . '::' . $method2 . '()' . PHP_EOL . '# ' . $file2 . ':' . $line2 . PHP_EOL)
		;
	}

	public function test__toStringWithSkip()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($score = new \mock\atoum\test\score())
			->and($this->calling($score)->getLastSkippedMethod[1] = array(
					'class' => $class1 = uniqid(),
					'method' => $method1 = uniqid(),
					'message' => $message1 = uniqid()
				)
			)
			->and($this->calling($score)->getLastSkippedMethod[2] = array(
					'class' => $class2 = uniqid(),
					'method' => $method2 = uniqid(),
					'message' => ($message2 = uniqid()) . PHP_EOL . ($otherMessage2 = uniqid()) . PHP_EOL . ($anotherMessage2 = uniqid())
				)
			)
			->and($test = new \mock\mageekguy\atoum\test())
			->and($this->calling($test)->getScore = $score)
			->and($field = new testedClass())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::skipped, $test))
			->then
				->castToString($field)->isEqualTo('ok 1 # SKIP ' . $class1 . '::' . $method1 . '()' . PHP_EOL .
					'# ' . $message1 . PHP_EOL
				)
			->if($field->handleEvent(atoum\test::skipped, $test))
			->then
				->castToString($field)->isEqualTo('ok 2 # SKIP ' . $class2 . '::' . $method2 . '()' . PHP_EOL .
					'# ' . $message2 . PHP_EOL .
					'# ' . $otherMessage2 . PHP_EOL .
					'# ' . $anotherMessage2 .
					PHP_EOL
				)
			->if($score->getMockController()->resetCalls())
			->and($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::skipped, $test))
			->then
				->castToString($field)->isEqualTo('ok 1 # SKIP ' . $class1 . '::' . $method1 . '()' . PHP_EOL .
					'# ' . $message1 . PHP_EOL
				)
			->if($field->handleEvent(atoum\test::skipped, $test))
			->then
				->castToString($field)->isEqualTo('ok 2 # SKIP ' . $class2 . '::' . $method2 . '()' . PHP_EOL .
					'# ' . $message2 . PHP_EOL .
					'# ' . $otherMessage2 . PHP_EOL .
					'# ' . $anotherMessage2 .
					PHP_EOL
				)
		;
	}
}
