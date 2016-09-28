<?php

namespace mageekguy\atoum\tests\units\report\fields\test\event;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\exceptions,
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

	public function test__toString()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test())
			->and($this->calling($test)->getClass = $class = uniqid())
			->and($this->calling($test)->getCurrentMethod = $method = uniqid())
			->and($field = new testedClass())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::success, $test))
			->then
				->castToString($field)->isEqualTo('ok 1' . PHP_EOL . '# ' . $class . '::' . $method . '()' . PHP_EOL)
			->if($this->calling($test)->getCurrentMethod = $otherMethod = uniqid())
			->and($field->handleEvent(atoum\test::success, $test))
			->then
				->castToString($field)->isEqualTo('ok 2' . PHP_EOL . '# ' . $class . '::' . $otherMethod . '()' . PHP_EOL)
			->if($this->calling($test)->getClass = $otherClass = uniqid())
			->and($this->calling($test)->getCurrentMethod = $thridMethod = uniqid())
			->and($field->handleEvent(atoum\test::success, $test))
			->then
				->castToString($field)->isEqualTo('ok 3' . PHP_EOL . '# ' . $otherClass . '::' . $thridMethod . '()' . PHP_EOL)
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
					'file' => $file1 = uniqid()
				)
			)
			->and($this->calling($score)->getLastVoidMethod[2] = array(
					'class' => $class2 = uniqid(),
					'method' => $method2 = uniqid(),
					'file' => $file2 = uniqid()
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
				->castToString($field)->isEqualTo('not ok 1 # TODO ' . $class1 . '::' . $method1 . '()' . PHP_EOL . '# ' . $file1 . PHP_EOL)
			->if($field->handleEvent(atoum\test::void, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 # TODO ' . $class2 . '::' . $method2 . '()' . PHP_EOL . '# ' . $file2 . PHP_EOL)
			->if($score->getMockController()->resetCalls())
			->and($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::void, $test))
			->then
				->castToString($field)->isEqualTo('not ok 1 # TODO ' . $class1 . '::' . $method1 . '()' . PHP_EOL . '# ' . $file1 . PHP_EOL)
			->if($field->handleEvent(atoum\test::void, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 # TODO ' . $class2 . '::' . $method2 . '()' . PHP_EOL . '# ' . $file2 . PHP_EOL)
		;
	}

	public function test__toStringWithSkip()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($score = new \mock\atoum\test\score())
			->and($this->calling($score)->getLastSkippedMethod[1] = array(
					'file' => $file1 = uniqid(),
					'class' => $class1 = uniqid(),
					'method' => $method1 = uniqid(),
					'line' => $line1 = rand(1, PHP_INT_MAX),
					'message' => $message1 = uniqid()
				)
			)
			->and($this->calling($score)->getLastSkippedMethod[2] = array(
					'file' => $file2 = uniqid(),
					'class' => $class2 = uniqid(),
					'method' => $method2 = uniqid(),
					'line' => $line2 = rand(1, PHP_INT_MAX),
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
					'# ' . $message1 . PHP_EOL .
					'# ' . $file1 . ':' . $line1 . PHP_EOL
				)
			->if($field->handleEvent(atoum\test::skipped, $test))
			->then
				->castToString($field)->isEqualTo('ok 2 # SKIP ' . $class2 . '::' . $method2 . '()' . PHP_EOL .
					'# ' . $message2 . PHP_EOL .
					'# ' . $otherMessage2 . PHP_EOL .
					'# ' . $anotherMessage2 . PHP_EOL .
					'# ' . $file2 . ':' . $line2 . PHP_EOL
				)
			->if($score->getMockController()->resetCalls())
			->and($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::skipped, $test))
			->then
				->castToString($field)->isEqualTo('ok 1 # SKIP ' . $class1 . '::' . $method1 . '()' . PHP_EOL .
					'# ' . $message1 . PHP_EOL .
					'# ' . $file1 . ':' . $line1 . PHP_EOL
				)
			->if($field->handleEvent(atoum\test::skipped, $test))
			->then
				->castToString($field)->isEqualTo('ok 2 # SKIP ' . $class2 . '::' . $method2 . '()' . PHP_EOL .
					'# ' . $message2 . PHP_EOL .
					'# ' . $otherMessage2 . PHP_EOL .
					'# ' . $anotherMessage2 . PHP_EOL .
					'# ' . $file2 . ':' . $line2 . PHP_EOL
				)
		;
	}

	public function test__toStringWithErrors()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($score = new \mock\atoum\test\score())
			->and($test = new \mock\mageekguy\atoum\test())
			->and($this->calling($test)->getScore = $score)
			->and($this->calling($test)->getClass = $class = uniqid())
			->and($this->calling($test)->getCurrentMethod[1] = $method = uniqid())
			->and($this->calling($test)->getCurrentMethod[2] = $otherMethod = uniqid())
			->and($this->calling($score)->getLastErroredMethod[1] = $firstError = array(
					'case' => $case = uniqid(),
					'dataSetKey' => $dataSetKey = uniqid(),
					'dataSetProvider' => $dataSetProvider = uniqid(),
					'class' => $class,
					'method' => $method,
					'file' => $file = uniqid(),
					'line' => $line = rand(1, PHP_INT_MAX),
					'type' => $type = rand(1, E_ALL),
					'message' => $message = uniqid(),
					'errorFile' => $errorFile = uniqid(),
					'errorLine' => $errorLine = rand(1, PHP_INT_MAX)
				)
			)
			->and($this->calling($score)->getLastErroredMethod[2] = $otherError = array(
					'case' => null,
					'dataSetKey' => null,
					'dataSetProvider' => null,
					'class' => $class,
					'method' => $otherMethod,
					'file' => $otherFile = uniqid(),
					'line' => $otherLine = rand(1, PHP_INT_MAX),
					'type' => $otherType = rand(1, E_ALL),
					'message' => $otherMessage = uniqid(),
					'errorFile' => null,
					'errorLine' => null
				)
			)
			->and($this->calling($score)->getErrors = array($firstError, $otherError))
			->if($field = new testedClass())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::error, $test))
			->then
				->castToString($field)->isEqualTo('not ok 1 - ' . $class . '::' . $method . '()' . PHP_EOL . '# ' . atoum\asserters\error::getAsString($type) . ' : ' . $message . PHP_EOL . '# ' . $errorFile . ':' . $errorLine . PHP_EOL)
			->if($field->handleEvent(atoum\test::error, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 - ' . $class . '::' . $otherMethod . '()' . PHP_EOL . '# ' . atoum\asserters\error::getAsString($otherType) . ' : ' . $otherMessage . PHP_EOL . '# ' . $otherFile . ':' . $otherLine . PHP_EOL)
		;
	}

	public function test__toStringWithException()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($score = new \mock\atoum\test\score())
			->and($test = new \mock\mageekguy\atoum\test())
			->and($this->calling($test)->getScore = $score)
			->and($this->calling($test)->getClass = $class = uniqid())
			->and($this->calling($test)->getCurrentMethod[1] = $method = uniqid())
			->and($this->calling($test)->getCurrentMethod[2] = $otherMethod = uniqid())
			->and($this->calling($score)->getLastException[1] = array(
					'case' => null,
					'dataSetKey' => null,
					'dataSetProvider' => null,
					'class' => $class,
					'method' => $method,
					'file' => $file = uniqid(),
					'line' => $line = rand(1, PHP_INT_MAX),
					'value' => $exception = uniqid()
				)
			)
			->and($this->calling($score)->getLastException[2] = array(
					'case' => null,
					'dataSetKey' => null,
					'dataSetProvider' => null,
					'class' => $class,
					'method' => $otherMethod,
					'file' => $otherFile = uniqid(),
					'line' => $otherLine = rand(1, PHP_INT_MAX),
					'value' => $otherException = uniqid()
				)
			)
			->and($field = new testedClass())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::exception, $test))
			->then
				->castToString($field)->isEqualTo('not ok 1 - ' . $class . '::' . $method . '()' . PHP_EOL . '# ' . $exception . PHP_EOL . '# ' . $file . ':' . $line . PHP_EOL)
			->if($field->handleEvent(atoum\test::exception, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 - ' . $class . '::' . $otherMethod . '()' . PHP_EOL . '# ' . $otherException . PHP_EOL . '# ' . $otherFile . ':' . $otherLine . PHP_EOL)
		;
	}

	public function test__toStringWithUncompleteMethods()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($score = new \mock\atoum\test\score())
			->and($test = new \mock\mageekguy\atoum\test())
			->and($this->calling($test)->getScore = $score)
			->and($this->calling($test)->getPath = $file = uniqid())
			->and($this->calling($test)->getClass = $class = uniqid())
			->and($this->calling($test)->getCurrentMethod[1] = $method = uniqid())
			->and($this->calling($test)->getCurrentMethod[2] = $otherMethod = uniqid())
			->and($this->calling($score)->getLastUncompleteMethod[1] = array(
					'file' => $file,
					'class' => $class,
					'method' => $method,
					'exitCode' => rand(1, PHP_INT_MAX),
					'output' => $output = uniqid()
				)
			)
			->and($this->calling($score)->getLastUncompleteMethod[2] = $this->calling($score)->getLastUncompleteMethod[3] = array(
					'file' => $file,
					'class' => $class,
					'method' => $otherMethod,
					'exitCode' => rand(1, PHP_INT_MAX),
					'output' => $otherOutput = uniqid()
				)
			)
			->and($this->calling($score)->getLastUncompleteMethod[4] = array(
					'file' => $file,
					'class' => $class,
					'method' => $thirdMethod = uniqid(),
					'exitCode' => $thirdExitCode = rand(1, PHP_INT_MAX),
					'output' => null
				)
			)
			->and($field = new testedClass())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::uncompleted, $test))
			->then
				->castToString($field)->isEqualTo('not ok 1 - ' . $class . '::' . $method . '()' . PHP_EOL . '# ' . $output . PHP_EOL . '# ' . $file . PHP_EOL)
			->if($field->handleEvent(atoum\test::uncompleted, $test))
			->then
				->castToString($field)->isEqualTo('not ok 2 - ' . $class . '::' . $otherMethod . '()' . PHP_EOL . '# ' . $otherOutput . PHP_EOL . '# ' . $file . PHP_EOL)
			->if($this->calling($score)->getLastErroredMethod = array(
					'errorFile' => $file,
					'class' => $class,
					'method' => $otherMethod,
					'type' => $errorType = 'error', //uniqid()
					'message' => ($errorMessageFirstLine = 'line1') . PHP_EOL . ($errorMessageSecondLine = 'line2')
				)
			)
			->and($field->handleEvent(atoum\test::uncompleted, $test))
			->then
				->castToString($field)->isEqualTo('not ok 3 - ' . $class . '::' . $otherMethod . '()' . PHP_EOL . '# ' . $errorType . ' : ' . $errorMessageFirstLine . PHP_EOL . '# ' . $errorMessageSecondLine . PHP_EOL . '# ' . $file . PHP_EOL)
			->if($field->handleEvent(atoum\test::uncompleted, $test))
			->then
				->castToString($field)->isEqualTo('not ok 4 - ' . $class . '::' . $thirdMethod . '()' . PHP_EOL . '# uncomplete method' . PHP_EOL . '# ' . $file . PHP_EOL)
		;
	}

	public function test__toStringWithRuntimeException()
	{
		$this
			->mockGenerator->shunt('__construct')
			->if($score = new \mock\atoum\test\score())
			->and($test = new \mock\mageekguy\atoum\test())
			->and($this->calling($test)->getScore = $score)
			->and($this->calling($test)->getClass = $class = uniqid())
			->and($this->calling($test)->getCurrentMethod = $method = uniqid())
			->and($this->calling($score)->getLastRuntimeException = new exceptions\runtime())
			->and($field = new testedClass())
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\runner::runStart, $test))
			->then
				->castToString($field)->isEmpty()
			->if($field->handleEvent(atoum\test::runtimeException, $test))
			->then
				->castToString($field)->isEqualTo('Bail out!' . PHP_EOL)
			->if($this->calling($score)->getLastRuntimeException = new exceptions\runtime($message = uniqid()))
			->and($field->handleEvent(atoum\test::runtimeException, $test))
			->then
				->castToString($field)->isEqualTo('Bail out! ' . $message . PHP_EOL)
		;
	}
}
