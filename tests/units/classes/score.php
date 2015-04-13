<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

require_once __DIR__ . '/../runner.php';

class score extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
				->array($score->getSkippedMethods())->isEmpty()
				->object($score->getCoverage())->isInstanceOf('mageekguy\atoum\score\coverage')
			->and($score = new atoum\score($coverage = new atoum\score\coverage()))
			->then
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
				->array($score->getSkippedMethods())->isEmpty()
				->object($score->getCoverage())->isIdenticalTo($coverage)
		;
	}

	public function testAddException()
	{
		$this
			->if($score = new atoum\score())
			->then
				->object($score->addException($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $exception = new \exception()))->isIdenticalTo($score)
				->array($score->getExceptions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'value' => (string) $exception
						)
					)
				)
				->integer($score->getExceptionNumber())->isEqualTo(1)
				->object($score->addException($otherFile = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherLine = rand(1, PHP_INT_MAX), $otherException = new \exception()))->isIdenticalTo($score)
					->array($score->getExceptions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'value' => (string) $exception
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'value' => (string) $otherException
						)
					)
				)
		;
	}

	public function testAddPass()
	{
		$this
			->if($score = new atoum\score())
			->then
				->object($score->addPass())->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(1)
				->object($score->addPass())->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(2)
		;
	}

	public function testAddFail()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->addFail($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $asserter = new atoum\asserters\integer(new atoum\asserter\generator()), $reason = uniqid()))->isGreaterThan(0)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'asserter' => $asserter,
							'fail' => $reason
						)
					)
				)
				->integer($score->addFail($otherFile = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherLine = rand(1, PHP_INT_MAX), $otherAsserter = new atoum\asserters\integer(new atoum\asserter\generator()), $otherReason = uniqid()))->isGreaterThan(0)
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'asserter' => $asserter,
							'fail' => $reason
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'asserter' => $otherAsserter,
							'fail' => $otherReason
						)
					)
				)
		;
	}

	public function testAddError()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getErrors())->isEmpty()
				->integer($score->getErrorNumber())->isZero()
				->object($score->addError($file = 'file1', $class = 'class1', $method = 'method1', $line = 1, $type = 5, $message = 'message1', $errorFile = 'errorFile1', $errorLine = 2))->isIdenticalTo($score)
				->array($score->getErrors())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $message,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						)
					)
				)
				->integer($score->getErrorNumber())->isEqualTo(1)
				->object($score->addError($otherFile = 'file2', $otherClass = 'class2', $otherMethod = 'method2', $otherLine = 10, $otherType = 15, $otherMessage = 'message2', $otherErrorFile = 'errorFile2', $otherErrorLine = 20))->isIdenticalTo($score)
				->array($score->getErrors())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $message,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'type' => $otherType,
							'message' => $otherMessage,
							'errorFile' => $otherErrorFile,
							'errorLine' => $otherErrorLine
						)
					)
				)
				->integer($score->getErrorNumber())->isEqualTo(2)
				->object($score->addError($file, $class, $method, $line, $type, $anAnotherMessage = 'message1.1', $errorFile, $errorLine))->isIdenticalTo($score)
				->array($score->getErrors())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $message,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $anAnotherMessage,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						),
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'type' => $otherType,
							'message' => $otherMessage,
							'errorFile' => $otherErrorFile,
							'errorLine' => $otherErrorLine
						)
					)
				)
				->integer($score->getErrorNumber())->isEqualTo(3)
				->object($score->addError($file, $class, $method, $line + 1, $type, ("   \t	\t" . $messageWithWhitespace = 'message with withespace' . "	  \t	" . PHP_EOL), $errorFile, $errorLine))->isIdenticalTo($score)
				->array($score->getErrors())
					->contains(array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $anAnotherMessage,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						)
					)
					->contains(array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'type' => $type,
							'message' => $message,
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						)
					)
					->contains(array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line + 1,
							'type' => $type,
							'message' => trim($messageWithWhitespace),
							'errorFile' => $errorFile,
							'errorLine' => $errorLine
						)
					)
					->contains(array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $otherClass,
							'method' => $otherMethod,
							'file' => $otherFile,
							'line' => $otherLine,
							'type' => $otherType,
							'message' => $otherMessage,
							'errorFile' => $otherErrorFile,
							'errorLine' => $otherErrorLine
						)
					)
				->integer($score->getErrorNumber())->isEqualTo(4)
		;
	}

	public function testAddOutput()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getOutputs())->isEmpty()
				->integer($score->getOutputNumber())->isZero()
				->object($score->addOutput($file = uniqid(), $class = uniqid(), $method = uniqid(), $output = uniqid()))->isIdenticalTo($score)
				->array($score->getOutputs())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $output
						)
					)
				)
				->integer($score->getOutputNumber())->isEqualTo(1)
				->object($score->addOutput($file = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherOutput = uniqid()))->isIdenticalTo($score)
				->array($score->getOutputs())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $output
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherOutput
						)
					)
				)
				->integer($score->getOutputNumber())->isEqualTo(2)
				->object($score->addOutput($file = uniqid(), $class, $method, $moreOutput = uniqid()))->isIdenticalTo($score)
				->array($score->getOutputs())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $output
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherOutput
						),
						array(
							'class' => $class,
							'method' => $method,
							'value' => $moreOutput
						)
					)
				)
				->integer($score->getOutputNumber())->isEqualTo(3)
		;
	}

	public function testAddDuration()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getDurations())->isEmpty()
				->integer($score->getDurationNumber())->isZero()
				->object($score->addDuration($path = uniqid(), $class = uniqid(), $method = uniqid(), $duration = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(1)
				->object($score->addDuration($otherPath = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherDuration = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherDuration,
							'path' => $otherPath
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(2)
				->object($score->addDuration(uniqid(), uniqid(), uniqid(), 0))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherDuration,
							'path' => $otherPath
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(2)
				->object($score->addDuration(uniqid(), uniqid(), uniqid(), - rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherDuration,
							'path' => $otherPath
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(2)
				->object($score->addDuration($path, $class, $method, $moreDuration = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getDurations())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $duration,
							'path' => $path
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherDuration,
							'path' => $otherPath
						),
						array(
							'class' => $class,
							'method' => $method,
							'value' => $moreDuration,
							'path' => $path
						)
					)
				)
				->integer($score->getDurationNumber())->isEqualTo(3)
		;
	}

	public function testAddMemoryUsage()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getMemoryUsages())->isEmpty()
				->object($score->addMemoryUsage($file = uniqid(), $class = uniqid(), $method = uniqid(), $memoryUsage = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						)
					)
				)
				->object($score->addMemoryUsage($otherFile = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherMemoryUsage = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherMemoryUsage
						)
					)
				)
				->object($score->addMemoryUsage(uniqid(), uniqid(), uniqid(), 0))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherMemoryUsage
						)
					)
				)
				->object($score->addMemoryUsage(uniqid(), uniqid(), uniqid(), - rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherMemoryUsage
						)
					)
				)
				->object($score->addMemoryUsage($file, $class, $method, $moreMemoryUsage = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->array($score->getMemoryUsages())->isEqualTo(array(
						array(
							'class' => $class,
							'method' => $method,
							'value' => $memoryUsage
						),
						array(
							'class' => $otherClass,
							'method' => $otherMethod,
							'value' => $otherMemoryUsage
						),
						array(
							'class' => $class,
							'method' => $method,
							'value' => $moreMemoryUsage
						)
					)
				)
		;
	}

	public function testAddUncompletedMethod()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getUncompletedMethods())->isEmpty()
				->object($score->addUncompletedMethod($file = uniqid(), $class = uniqid(), $method = uniqid(), $exitCode = rand(1, PHP_INT_MAX), $output = uniqid()))->isIdenticalTo($score)
				->array($score->getUncompletedMethods())->isEqualTo(array(
						array(
							'file' => $file,
							'class' => $class,
							'method' => $method,
							'exitCode' => $exitCode,
							'output' => $output,
						)
					)
				)
				->object($score->addUncompletedMethod($otherFile = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherExitCode = rand(1, PHP_INT_MAX), $otherOutput = uniqid()))->isIdenticalTo($score)
				->array($score->getUncompletedMethods())->isEqualTo(array(
						array(
							'file' => $file,
							'class' => $class,
							'method' => $method,
							'exitCode' => $exitCode,
							'output' => $output,
						),
						array(
							'file' => $otherFile,
							'class' => $otherClass,
							'method' => $otherMethod,
							'exitCode' => $otherExitCode,
							'output' => $otherOutput,
						)
					)
				)
		;
	}

	public function testAddSkippedMethod()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getSkippedMethods())->isEmpty()
				->object($score->addSkippedMethod($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $message = uniqid()))->isIdenticalTo($score)
				->array($score->getSkippedMethods())->isEqualTo(array(
						array(
							'file' => $file,
							'class' => $class,
							'method' => $method,
							'line' => $line,
							'message' => $message
						)
					)
				)
		;
	}

	public function testAddRuntimeException()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getRuntimeExceptions())->isEmpty()
				->integer($score->getRuntimeExceptionNumber())->isZero()
				->object($score->addRuntimeException(uniqid(), uniqid(), uniqid(), $exception = new atoum\test\exceptions\runtime()))->isIdenticalTo($score)
				->array($score->getRuntimeExceptions())->isEqualTo(array(
						$exception
					)
				)
				->integer($score->getRuntimeExceptionNumber())->isEqualTo(1)
				->object($score->addRuntimeException(uniqid(), uniqid(), uniqid(), $otherException = new atoum\test\exceptions\runtime()))->isIdenticalTo($score)
				->array($score->getRuntimeExceptions())->isEqualTo(array(
						$exception,
						$otherException
					)
				)
				->integer($score->getRuntimeExceptionNumber())->isEqualTo(2)
		;
	}

	public function testSetCoverage()
	{
		$this
			->if($score = new atoum\score())
			->then
				->object($score->setCoverage($coverage = new atoum\score\coverage()))->isIdenticalTo($score)
				->object($score->getCoverage())->isIdenticalTo($coverage)
		;
	}

	public function testGetExceptionNumber()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getExceptionNumber())->isZero()
			->if($score->addException(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new \exception()))
			->then
				->integer($score->getExceptionNumber())->isEqualTo(1)
			->if($score->addException(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new \exception()))
			->then
				->integer($score->getExceptionNumber())->isEqualTo(2)
		;
	}

	public function testGetFailNumber()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getFailNumber())->isZero()
			->if($score->addFail(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->then
				->integer($score->getFailNumber())->isEqualTo(1)
			->if($score->addFail(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->then
				->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testGetFailAssertions()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getFailAssertions())->isEmpty()
			->if($score->addPass())
			->then
				->array($score->getFailAssertions())->isEmpty()
			->if($score->addFail($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $asserter = new atoum\asserters\integer(new atoum\asserter\generator()), $reason = uniqid()))
			->then
				->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'dataSetKey' => null,
							'dataSetProvider' => null,
							'class' => $class,
							'method' => $method,
							'file' => $file,
							'line' => $line,
							'asserter' => $asserter,
							'fail' => $reason
						)
					)
				)
		;
	}

	public function testGetLastFailAssertion()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->getLastFailAssertion())->isNull()
			->if($score->addPass())
			->then
				->variable($score->getLastFailAssertion())->isNull()
			->if($score->addFail($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $asserter = new atoum\asserters\integer(new atoum\asserter\generator()), $reason = uniqid()))
			->then
				->array($score->getLastFailAssertion())->isEqualTo(array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'asserter' => $asserter,
						'fail' => $reason
					)
				)
		;
	}

	public function testGetLastVoidMethod()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->getLastVoidMethod())->isNull()
			->if($score->addPass())
			->then
				->variable($score->getLastVoidMethod())->isNull()
			->if($score->addVoidMethod($file = uniqid(), $class = uniqid(), $method = uniqid()))
			->then
				->array($score->getLastVoidMethod())->isEqualTo(array(
						'file' => $file,
						'class' => $class,
						'method' => $method
					)
				)
		;
	}

	public function testGetLastSkippedMethod()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->getLastSkippedMethod())->isNull()
			->if($score->addPass())
			->then
				->variable($score->getLastSkippedMethod())->isNull()
			->if($score->addSkippedMethod($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $message = uniqid()))
			->then
				->array($score->getLastSkippedMethod())->isEqualTo(array(
						'file' => $file,
						'class' => $class,
						'method' => $method,
						'line' => $line,
						'message' => $message
					)
				)
		;
	}

	public function testGetLastErroredMethod()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->getLastErroredMethod())->isNull()
			->if($score->addPass())
			->then
				->variable($score->getLastErroredMethod())->isNull()
			->if($score->addError($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $type = rand(E_ERROR, E_USER_DEPRECATED), $message = uniqid()))
			->then
				->array($score->getLastErroredMethod())->isEqualTo(array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'type' => $type,
						'message' => trim($message),
						'errorFile' => null,
						'errorLine' => null
					)
				)
			->if($score->addError($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $type = rand(E_ERROR, E_USER_DEPRECATED), $message = uniqid(), $errorFile = uniqid(), $errorLine = rand(1, PHP_INT_MAX), $case = uniqid(), $dataSetKey = uniqid(), $dataSetProvider = uniqid()))
			->then
				->array($score->getLastErroredMethod())->isEqualTo(array(
						'case' => $case,
						'dataSetKey' => $dataSetKey,
						'dataSetProvider' => $dataSetProvider,
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'type' => $type,
						'message' => trim($message),
						'errorFile' => $errorFile,
						'errorLine' => $errorLine
					)
				)
		;
	}

	public function testGetLastException()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->getLastException())->isNull()
			->if($score->addPass())
			->then
				->variable($score->getLastException())->isNull()
			->if($score->addException($file = uniqid(), $class = uniqid(), $method = uniqid(), $line = rand(1, PHP_INT_MAX), $exception = new \exception()))
			->then
				->array($score->getLastException())->isEqualTo(array(
						'case' => null,
						'dataSetKey' => null,
						'dataSetProvider' => null,
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'value' => (string) $exception
					)
				)
			->if($score->addException($otherFile = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherLine = rand(1, PHP_INT_MAX), $otherException = new \exception(), $case = uniqid(), $dataSetKey = uniqid(), $dataSetProvider = uniqid()))
			->then
				->array($score->getLastException())->isEqualTo(array(
						'case' => $case,
						'dataSetKey' => $dataSetKey,
						'dataSetProvider' => $dataSetProvider,
						'class' => $otherClass,
						'method' => $otherMethod,
						'file' => $otherFile,
						'line' => $otherLine,
						'value' => (string) $otherException
					)
				)
		;
	}

	public function testGetLastUncompleteMethod()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->getLastUncompleteMethod())->isNull()
			->if($score->addPass())
			->then
				->variable($score->getLastUncompleteMethod())->isNull()
			->if($score->addUncompletedMethod($file = uniqid(), $class = uniqid(), $method = uniqid(), $exitCode = rand(1, PHP_INT_MAX), $output = uniqid()))
			->then
				->array($score->getLastUncompleteMethod())->isEqualTo(array(
						'file' => $file,
						'class' => $class,
						'method' => $method,
						'exitCode' => $exitCode,
						'output' => $output,
					)
				)
			->if($score->addUncompletedMethod($otherFile = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherExitCode = rand(1, PHP_INT_MAX), $otherOutput = uniqid()))
			->then
				->array($score->getLastUncompleteMethod())->isEqualTo(array(
						'file' => $otherFile,
						'class' => $otherClass,
						'method' => $otherMethod,
						'exitCode' => $otherExitCode,
						'output' => $otherOutput,
					)
				)
		;
	}

	public function testGetLastRuntimeException()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->getLastRuntimeException())->isNull()
			->if($score->addPass())
			->then
				->variable($score->getLastRuntimeException())->isNull()
			->if($score->addRuntimeException($file = uniqid(), $class = uniqid(), $method = uniqid(), $exception = new exceptions\runtime()))
			->then
				->object($score->getLastRuntimeException())->isIdenticalTo($exception)
			->if($score->addRuntimeException($otherFile = uniqid(), $otherClass = uniqid(), $otherMethod = uniqid(), $otherException = new exceptions\runtime()))
			->then
				->object($score->getLastRuntimeException())->isIdenticalTo($otherException)
		;
	}

	public function testGetPassAssertions()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getPassNumber())->isZero()
			->if($score->addFail(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->then
				->integer($score->getPassNumber())->isZero()
			->if($score->addPass())
			->then
				->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testGetCoverage()
	{
		$this
			->if($score = new atoum\score())
			->then
				->object($coverage = $score->getCoverage())->isInstanceOf('mageekguy\atoum\score\coverage')
		;
	}

	public function testGetMethodsWithFail()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getMethodsWithFail())->isEmpty()
			->if($asserter = new atoum\asserters\integer(new atoum\asserter\generator()))
			->and($score->addFail(uniqid(), $class = uniqid(), $classMethod = uniqid(), rand(1, PHP_INT_MAX), $asserter, uniqid()))
			->then
				->array($score->getMethodsWithFail())->isEqualTo(array($class => array($classMethod)))
			->if($score->addFail(uniqid(), $class, $classOtherMethod = uniqid(), rand(1, PHP_INT_MAX), $asserter, uniqid()))
			->then
				->array($score->getMethodsWithFail())->isEqualTo(array($class => array($classMethod, $classOtherMethod)))
			->if($score->addFail(uniqid(), $otherClass = uniqid(), $otherClassMethod = uniqid(), rand(1, PHP_INT_MAX), $asserter, uniqid()))
			->then
				->array($score->getMethodsWithFail())->isEqualTo(array(
						$class => array($classMethod, $classOtherMethod),
						$otherClass => array($otherClassMethod)
					)
				)
		;
	}

	public function testGetMethodsWithError()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getMethodsWithError())->isEmpty()
			->if($score->addError(uniqid(), $class = uniqid(), $classMethod = uniqid(), rand(1, PHP_INT_MAX), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->array($score->getMethodsWithError())->isEqualTo(array($class => array($classMethod)))
			->if($score->addError(uniqid(), $class, $classOtherMethod = uniqid(), rand(1, PHP_INT_MAX), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->array($score->getMethodsWithError())->isEqualTo(array($class => array($classMethod, $classOtherMethod)))
			->if($score->addError(uniqid(), $otherClass = uniqid(), $otherClassMethod = uniqid(), rand(1, PHP_INT_MAX), rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->array($score->getMethodsWithError())->isEqualTo(array(
						$class => array($classMethod, $classOtherMethod),
						$otherClass => array($otherClassMethod)
					)
				)
		;
	}

	public function testGetMethodsWithException()
	{
		$this
			->if($score = new atoum\score())
			->then
				->array($score->getMethodsWithError())->isEmpty()
			->if($score->addException(uniqid(), $class = uniqid(), $classMethod = uniqid(), rand(1, PHP_INT_MAX), new \exception()))
			->then
				->array($score->getMethodsWithException())->isEqualTo(array($class => array($classMethod)))
			->if($score->addException(uniqid(), $class, $classOtherMethod = uniqid(), rand(1, PHP_INT_MAX), new \exception()))
			->then
				->array($score->getMethodsWithException())->isEqualTo(array($class => array($classMethod, $classOtherMethod)))
			->if($score->addException(uniqid(), $otherClass = uniqid(), $otherClassMethod = uniqid(), rand(1, PHP_INT_MAX), new \exception()))
			->then
				->array($score->getMethodsWithException())->isEqualTo(array(
						$class => array($classMethod, $classOtherMethod),
						$otherClass => array($otherClassMethod)
					)
				)
		;
	}

	public function testReset()
	{
		$this
			->if($score = new atoum\score())
			->then
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getRuntimeExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
				->object($score->reset())->isIdenticalTo($score)
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getRuntimeExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
			->if($score
				->addPass()
				->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception())
				->addRuntimeException(uniqid(), uniqid(), uniqid(), new atoum\exceptions\runtime())
				->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX))
				->addOutput(uniqid(), uniqid(), uniqid(), uniqid())
				->addDuration(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX))
				->addMemoryUsage(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX))
				->addUncompletedMethod(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid())
			)
			->and($score->addFail(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->then
				->integer($score->getPassNumber())->isGreaterThan(0)
				->array($score->getFailAssertions())->isNotEmpty()
				->array($score->getExceptions())->isNotEmpty()
				->array($score->getRuntimeExceptions())->isNotEmpty()
				->array($score->getErrors())->isNotEmpty()
				->array($score->getOutputs())->isNotEmpty()
				->array($score->getDurations())->isNotEmpty()
				->array($score->getMemoryUsages())->isNotEmpty()
				->array($score->getUncompletedMethods())->isNotEmpty()
				->object($score->reset())->isIdenticalTo($score)
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getRuntimeExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
		;
	}

	public function testMerge()
	{
		$this
			->if($score = new atoum\score())
			->and($otherScore = new atoum\score())
			->then
				->object($score->merge($otherScore))->isIdenticalTo($score)
				->integer($score->getPassNumber())->isZero()
				->array($score->getFailAssertions())->isEmpty()
				->array($score->getExceptions())->isEmpty()
				->array($score->getRuntimeExceptions())->isEmpty()
				->array($score->getErrors())->isEmpty()
				->array($score->getOutputs())->isEmpty()
				->array($score->getDurations())->isEmpty()
				->array($score->getMemoryUsages())->isEmpty()
				->array($score->getUncompletedMethods())->isEmpty()
				->array($score->getSkippedMethods())->isEmpty()
			->if($score->addPass())
			->and($score->addFail(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->and($score->addException(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new \exception()))
			->and($score->addRuntimeException(uniqid(), uniqid(), uniqid(), new atoum\exceptions\runtime()))
			->and($score->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addOutput(uniqid(), uniqid(), uniqid(), uniqid()))
			->and($score->addDuration(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addMemoryUsage(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($score->addUncompletedMethod(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))
			->and($score->addSkippedMethod(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))
			->then
				->object($score->merge($otherScore))->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(1)
				->integer($score->getFailNumber())->isEqualTo(1)
				->integer($score->getExceptionNumber())->isEqualTo(1)
				->integer($score->getErrorNumber())->isEqualTo(1)
				->integer($score->getOutputNumber())->isEqualTo(1)
				->integer($score->getDurationNumber())->isEqualTo(1)
				->integer($score->getMemoryUsageNumber())->isEqualTo(1)
				->integer($score->getUncompletedMethodNumber())->isEqualTo(1)
				->integer($score->getSkippedMethodNumber())->isEqualTo(1)
			->if($otherScore->addPass())
			->and($otherScore->addFail(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new atoum\asserters\integer(new atoum\asserter\generator()), uniqid()))
			->and($otherScore->addException(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), new \exception()))
			->and($otherScore->addRuntimeException(uniqid(), uniqid(), uniqid(), new atoum\exceptions\runtime()))
			->and($otherScore->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($otherScore->addOutput(uniqid(), uniqid(), uniqid(), uniqid()))
			->and($otherScore->addDuration(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($otherScore->addMemoryUsage(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->and($otherScore->addUncompletedMethod(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))
			->and($otherScore->addSkippedMethod(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))
			->then
				->object($score->merge($otherScore))->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(2)
				->integer($score->getFailNumber())->isEqualTo(2)
				->integer($score->getExceptionNumber())->isEqualTo(2)
				->integer($score->getRuntimeExceptionNumber())->isEqualTo(2)
				->integer($score->getErrorNumber())->isEqualTo(2)
				->integer($score->getOutputNumber())->isEqualTo(2)
				->integer($score->getDurationNumber())->isEqualTo(2)
				->integer($score->getMemoryUsageNumber())->isEqualTo(2)
				->integer($score->getUncompletedMethodNumber())->isEqualTo(2)
				->integer($score->getSkippedMethodNumber())->isEqualTo(2)
				->object($score->merge($otherScore))->isIdenticalTo($score)
				->integer($score->getPassNumber())->isEqualTo(3)
				->integer($score->getFailNumber())->isEqualTo(3)
				->integer($score->getExceptionNumber())->isEqualTo(3)
				->integer($score->getRuntimeExceptionNumber())->isEqualTo(3)
				->integer($score->getErrorNumber())->isEqualTo(3)
				->integer($score->getOutputNumber())->isEqualTo(3)
				->integer($score->getDurationNumber())->isEqualTo(3)
				->integer($score->getMemoryUsageNumber())->isEqualTo(3)
				->integer($score->getUncompletedMethodNumber())->isEqualTo(3)
				->integer($score->getSkippedMethodNumber())->isEqualTo(3)
		;
	}

	public function testErrorExists()
	{
		$this
			->if($score = new atoum\score())
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX), rand(1, PHP_INT_MAX)))->isNull()
			->if($score->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $type = rand(1, PHP_INT_MAX - 1), $message = uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
			->if($score->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $type, $otherMessage = uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
			->if($score->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $otherType = $type + 1, $message, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
				->variable($score->errorExists($otherMessage, $otherType))->isNull()
				->integer($score->errorExists($message, $otherType))->isEqualTo(2)
				->integer($score->errorExists(null, $otherType))->isEqualTo(2)
			->if($score->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $otherType, $otherMessage, uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
				->integer($score->errorExists($otherMessage, $otherType))->isEqualTo(3)
				->integer($score->errorExists($message, $otherType))->isEqualTo(2)
				->integer($score->errorExists(null, $otherType))->isEqualTo(2)
			->if($score->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $otherType, $pattern = uniqid() . 'FOO' . uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->variable($score->errorExists(uniqid()))->isNull()
				->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
				->integer($score->errorExists($message))->isEqualTo(0)
				->integer($score->errorExists(null, $type))->isEqualTo(0)
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
				->integer($score->errorExists($otherMessage, $otherType))->isEqualTo(3)
				->integer($score->errorExists($message, $otherType))->isEqualTo(2)
				->integer($score->errorExists(null, $otherType))->isEqualTo(2)
				->integer($score->errorExists($pattern, $otherType))->isEqualTo(4)
				->variable($score->errorExists('/FOO/', $otherType))->isNull()
				->integer($score->errorExists('/FOO/', $otherType, true))->isEqualTo(4)
		;
	}

	public function testDeleteError()
	{
		$this
			->if($score = new atoum\score())
			->exception(function() use ($score, & $key) { $score->deleteError($key = rand(- PHP_INT_MAX, PHP_INT_MAX)); })
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Error key \'' . $key . '\' does not exist')
			->if($score->addError(uniqid(), uniqid(), uniqid(), rand(1, PHP_INT_MAX), $type = rand(1, PHP_INT_MAX), $message = uniqid(), uniqid(), rand(1, PHP_INT_MAX)))
			->then
				->integer($score->errorExists($message, $type))->isEqualTo(0)
				->object($score->deleteError(0))->isIdenticalTo($score)
				->variable($score->errorExists($message, $type))->isNull()
		;
	}
}
