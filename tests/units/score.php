<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../../runners/autorunner.php');

class score extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();

		$this->assert
			->collection($score->getPassAssertions())->isEmpty()
			->collection($score->getFailAssertions())->isEmpty()
			->collection($score->getExceptions())->isEmpty()
			->collection($score->getErrors())->isEmpty()
			->collection($score->getOutputs())->isEmpty()
			->collection($score->getDurations())->isEmpty()
			->collection($score->getMemoryUsages())->isEmpty()
		;
	}

	public function testAddException()
	{
		$score = new atoum\score();

		$file = uniqid();
		$line = rand(1, PHP_INT_MAX);
		$class = uniqid();
		$method = uniqid();
		$exception = new \exception();

		$this->assert
			->collection($score->getExceptions())->isEmpty()
			->integer($score->getExceptionNumber())->isZero()
			->object($score->addException($file, $line, $class, $method, $exception))->isIdenticalTo($score)
			->collection($score->getExceptions())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'value' => (string) $exception
					)
				)
			)
			->integer($score->getExceptionNumber())->isEqualTo(1)
		;

		$otherFile = uniqid();
		$otherLine = rand(1, PHP_INT_MAX);
		$otherClass = uniqid();
		$otherMethod = uniqid();
		$otherException = new \exception();

		$this->assert
			->object($score->addException($otherFile, $otherLine, $otherClass, $otherMethod, $otherException))->isIdenticalTo($score)
			->collection($score->getExceptions())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'value' => (string) $exception
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'file' => $otherFile,
						'line' => $otherLine,
						'value' => (string) $otherException
					)
				)
			)
			->integer($score->getExceptionNumber())->isEqualTo(2)
		;
	}

	public function testAddPass()
	{
		$score = new atoum\score();

		$file = uniqid();
		$line = rand(1, PHP_INT_MAX);
		$class = uniqid();
		$method = uniqid();
		$asserter = new atoum\asserters\integer($score, new atoum\locale());

		$this->assert
			->collection($score->getPassAssertions())->isEmpty()
			->integer($score->getPassNumber())->isZero()
			->object($score->addPass($file, $line, $class, $method, $asserter))->isIdenticalTo($score)
			->collection($score->getPassAssertions())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'asserter' => $asserter,
						'fail' => null
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(1)
		;

		$otherFile = uniqid();
		$otherLine = rand(1, PHP_INT_MAX);
		$otherClass = uniqid();
		$otherMethod = uniqid();
		$otherAsserter = new atoum\asserters\integer($score, new atoum\locale());

		$this->assert
			->object($score->addPass($otherFile, $otherLine, $otherClass, $otherMethod, $otherAsserter))->isIdenticalTo($score)
			->collection($score->getPassAssertions())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'asserter' => $asserter,
						'fail' => null
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'file' => $otherFile,
						'line' => $otherLine,
						'asserter' => $otherAsserter,
						'fail' => null
					)
				)
			)
			->integer($score->getPassNumber())->isEqualTo(2)
		;
	}

	public function testAddFail()
	{
		$score = new atoum\score();

		$file = uniqid();
		$line = rand(1, PHP_INT_MAX);
		$class = uniqid();
		$method = uniqid();
		$asserter = new atoum\asserters\integer($score, new atoum\locale());
		$reason = uniqid();

		$this->assert
			->collection($score->getFailAssertions())->isEmpty()
			->integer($score->getFailNumber())->isZero()
			->object($score->addFail($file, $line, $class, $method, $asserter, $reason))->isIdenticalTo($score)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'asserter' => $asserter,
						'fail' => $reason
					)
				)
			)
			->integer($score->getFailNumber())->isEqualTo(1)
		;

		$otherFile = uniqid();
		$otherLine = rand(1, PHP_INT_MAX);
		$otherClass = uniqid();
		$otherMethod = uniqid();
		$otherAsserter = new atoum\asserters\integer($score, new atoum\locale());
		$otherReason = uniqid();

		$this->assert
			->object($score->addFail($otherFile, $otherLine, $otherClass, $otherMethod, $otherAsserter, $otherReason))->isIdenticalTo($score)
			->collection($score->getFailAssertions())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'asserter' => $asserter,
						'fail' => $reason
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'file' => $otherFile,
						'line' => $otherLine,
						'asserter' => $otherAsserter,
						'fail' => $otherReason
					)
				)
			)
			->integer($score->getFailNumber())->isEqualTo(2)
		;
	}

	public function testAddError()
	{
		$score = new atoum\score();

		$file = uniqid();
		$line = rand(1, PHP_INT_MAX);
		$class = uniqid();
		$method = uniqid();
		$type = rand(1, PHP_INT_MAX);
		$message = uniqid();

		$this->assert
			->collection($score->getErrors())->isEmpty()
			->integer($score->getErrorNumber())->isZero()
			->object($score->addError($file, $line, $class, $method, $type, $message))->isIdenticalTo($score)
			->collection($score->getErrors())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'type' => $type,
						'message' => $message
					)
				)
			)
			->integer($score->getErrorNumber())->isEqualTo(1)
		;

		$otherFile = uniqid();
		$otherLine = rand(1, PHP_INT_MAX);
		$otherClass = uniqid();
		$otherMethod = uniqid();
		$otherType = rand(1, PHP_INT_MAX);
		$otherMessage = uniqid();

		$this->assert
			->object($score->addError($otherFile, $otherLine, $otherClass, $otherMethod, $otherType, $otherMessage))->isIdenticalTo($score)
			->collection($score->getErrors())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'type' => $type,
						'message' => $message
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'file' => $otherFile,
						'line' => $otherLine,
						'type' => $otherType,
						'message' => $otherMessage
					)
				)
			)
			->integer($score->getErrorNumber())->isEqualTo(2)
		;
	}

	public function testAddOutput()
	{
		$score = new atoum\score();

		$class = uniqid();
		$method = uniqid();
		$output = uniqid();

		$this->assert
			->collection($score->getOutputs())->isEmpty()
			->integer($score->getOutputNumber())->isZero()
			->object($score->addOutput($class, $method, $output))->isIdenticalTo($score)
			->collection($score->getOutputs())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'value' => $output
					)
				)
			)
			->integer($score->getOutputNumber())->isEqualTo(1)
		;

		$otherClass = uniqid();
		$otherMethod = uniqid();
		$otherOutput = uniqid();

		$this->assert
			->object($score->addOutput($otherClass, $otherMethod, $otherOutput))->isIdenticalTo($score)
			->collection($score->getOutputs())->isEqualTo(array(
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
		;

		$moreOutput = uniqid();

		$this->assert
			->object($score->addOutput($class, $method, $moreOutput))->isIdenticalTo($score)
			->collection($score->getOutputs())->isEqualTo(array(
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
		$score = new atoum\score();

		$class = uniqid();
		$method = uniqid();
		$duration = rand(1, PHP_INT_MAX);

		$this->assert
			->collection($score->getDurations())->isEmpty()
			->integer($score->getDurationNumber())->isZero()
			->object($score->addDuration($class, $method, $duration))->isIdenticalTo($score)
			->collection($score->getDurations())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'value' => $duration
					)
				)
			)
			->integer($score->getDurationNumber())->isEqualTo(1)
		;

		$otherClass = uniqid();
		$otherMethod = uniqid();
		$otherDuration = rand(1, PHP_INT_MAX);

		$this->assert
			->object($score->addDuration($otherClass, $otherMethod, $otherDuration))->isIdenticalTo($score)
			->collection($score->getDurations())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'value' => $duration
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherDuration
					)
				)
			)
			->integer($score->getDurationNumber())->isEqualTo(2)
		;

		$this->assert
			->object($score->addDuration(uniqid(), uniqid(), 0))->isIdenticalTo($score)
			->collection($score->getDurations())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'value' => $duration
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherDuration
					)
				)
			)
			->integer($score->getDurationNumber())->isEqualTo(2)
		;

		$this->assert
			->object($score->addDuration(uniqid(), uniqid(), - rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
			->collection($score->getDurations())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'value' => $duration
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherDuration
					)
				)
			)
			->integer($score->getDurationNumber())->isEqualTo(2)
		;

		$moreDuration = rand(1, PHP_INT_MAX);

		$this->assert
			->object($score->addDuration($class, $method, $moreDuration))->isIdenticalTo($score)
			->collection($score->getDurations())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'value' => $duration
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherDuration
					),
					array(
						'class' => $class,
						'method' => $method,
						'value' => $moreDuration
					)
				)
			)
			->integer($score->getDurationNumber())->isEqualTo(3)
		;
	}

	public function testAddMemoryUsage()
	{
		$score = new atoum\score();

		$class = uniqid();
		$method = uniqid();
		$memoryUsage = rand(1, PHP_INT_MAX);

		$this->assert
			->collection($score->getMemoryUsages())->isEmpty()
			->object($score->addMemoryUsage($class, $method, $memoryUsage))->isIdenticalTo($score)
			->collection($score->getMemoryUsages())->isEqualTo(array(
					array(
						'class' => $class,
						'method' => $method,
						'value' => $memoryUsage
					)
				)
			)
		;

		$otherClass = uniqid();
		$otherMethod = uniqid();
		$otherMemoryUsage = rand(1, PHP_INT_MAX);

		$this->assert
			->object($score->addMemoryUsage($otherClass, $otherMethod, $otherMemoryUsage))->isIdenticalTo($score)
			->collection($score->getMemoryUsages())->isEqualTo(array(
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
		;

		$this->assert
			->object($score->addMemoryUsage(uniqid(), uniqid(), 0))->isIdenticalTo($score)
			->collection($score->getMemoryUsages())->isEqualTo(array(
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
		;

		$this->assert
			->object($score->addMemoryUsage(uniqid(), uniqid(), - rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
			->collection($score->getMemoryUsages())->isEqualTo(array(
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
		;

		$moreMemoryUsage = rand(1, PHP_INT_MAX);

		$this->assert
			->object($score->addMemoryUsage($class, $method, $moreMemoryUsage))->isIdenticalTo($score)
			->collection($score->getMemoryUsages())->isEqualTo(array(
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

	public function testReset()
	{
		$score = new atoum\score($this);

		$this->assert
			->collection($score->getPassAssertions())->isEmpty()
			->collection($score->getFailAssertions())->isEmpty()
			->collection($score->getExceptions())->isEmpty()
			->collection($score->getErrors())->isEmpty()
			->collection($score->getOutputs())->isEmpty()
			->collection($score->getDurations())->isEmpty()
			->collection($score->getMemoryUsages())->isEmpty()
			->object($score->reset())->isIdenticalTo($score)
			->collection($score->getPassAssertions())->isEmpty()
			->collection($score->getFailAssertions())->isEmpty()
			->collection($score->getExceptions())->isEmpty()
			->collection($score->getErrors())->isEmpty()
			->collection($score->getOutputs())->isEmpty()
			->collection($score->getDurations())->isEmpty()
			->collection($score->getMemoryUsages())->isEmpty()
		;

		$score->addPass(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer($score, new atoum\locale(), rand(- PHP_INT_MAX, PHP_INT_MAX)));
		$score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer($score, new atoum\locale(), rand(- PHP_INT_MAX, PHP_INT_MAX)), uniqid());
		$score->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception());
		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid());
		$score->addOutput(uniqid(), uniqid(), uniqid());
		$score->addDuration(uniqid(), uniqid(), rand(1, PHP_INT_MAX));
		$score->addMemoryUsage(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->collection($score->getPassAssertions())->isNotEmpty()
			->collection($score->getFailAssertions())->isNotEmpty()
			->collection($score->getExceptions())->isNotEmpty()
			->collection($score->getErrors())->isNotEmpty()
			->collection($score->getOutputs())->isNotEmpty()
			->collection($score->getDurations())->isNotEmpty()
			->collection($score->getMemoryUsages())->isNotEmpty()
			->object($score->reset())->isIdenticalTo($score)
			->collection($score->getPassAssertions())->isEmpty()
			->collection($score->getFailAssertions())->isEmpty()
			->collection($score->getExceptions())->isEmpty()
			->collection($score->getErrors())->isEmpty()
			->collection($score->getOutputs())->isEmpty()
			->collection($score->getDurations())->isEmpty()
			->collection($score->getMemoryUsages())->isEmpty()
		;
	}

	public function testMerge()
	{
		$score = new atoum\score();
		$otherScore = new atoum\score();

		$this->assert
			->collection($score->getPassAssertions())->isEmpty()
			->collection($score->getFailAssertions())->isEmpty()
			->collection($score->getExceptions())->isEmpty()
			->collection($score->getErrors())->isEmpty()
			->collection($score->getOutputs())->isEmpty()
			->collection($score->getDurations())->isEmpty()
			->collection($score->getMemoryUsages())->isEmpty()
			->object($score->merge($otherScore))->isIdenticalTo($score)
			->collection($score->getPassAssertions())->isEmpty()
			->collection($score->getFailAssertions())->isEmpty()
			->collection($score->getExceptions())->isEmpty()
			->collection($score->getErrors())->isEmpty()
			->collection($score->getOutputs())->isEmpty()
			->collection($score->getDurations())->isEmpty()
			->collection($score->getMemoryUsages())->isEmpty()
		;

		$score->addPass(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer($score, new atoum\locale(), rand(- PHP_INT_MAX, PHP_INT_MAX)));
		$score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer($score, new atoum\locale(), rand(- PHP_INT_MAX, PHP_INT_MAX)), uniqid());
		$score->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception());
		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid());
		$score->addOutput(uniqid(), uniqid(), uniqid());
		$score->addDuration(uniqid(), uniqid(), rand(1, PHP_INT_MAX));
		$score->addMemoryUsage(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getExceptionNumber())->isEqualTo(1)
			->integer($score->getErrorNumber())->isEqualTo(1)
			->integer($score->getOutputNumber())->isEqualTo(1)
			->integer($score->getDurationNumber())->isEqualTo(1)
			->integer($score->getMemoryUsageNumber())->isEqualTo(1)
			->object($score->merge($otherScore))->isIdenticalTo($score)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->integer($score->getExceptionNumber())->isEqualTo(1)
			->integer($score->getErrorNumber())->isEqualTo(1)
			->integer($score->getOutputNumber())->isEqualTo(1)
			->integer($score->getDurationNumber())->isEqualTo(1)
			->integer($score->getMemoryUsageNumber())->isEqualTo(1)
		;

		$otherScore->addPass(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer($score, new atoum\locale(), rand(- PHP_INT_MAX, PHP_INT_MAX)));
		$otherScore->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer($score, new atoum\locale(), rand(- PHP_INT_MAX, PHP_INT_MAX)), uniqid());
		$otherScore->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception());
		$otherScore->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid());
		$otherScore->addOutput(uniqid(), uniqid(), uniqid());
		$otherScore->addDuration(uniqid(), uniqid(), rand(1, PHP_INT_MAX));
		$otherScore->addMemoryUsage(uniqid(), uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->integer($otherScore->getPassNumber())->isEqualTo(1)
			->integer($otherScore->getFailNumber())->isEqualTo(1)
			->integer($otherScore->getExceptionNumber())->isEqualTo(1)
			->integer($otherScore->getErrorNumber())->isEqualTo(1)
			->integer($otherScore->getOutputNumber())->isEqualTo(1)
			->integer($otherScore->getDurationNumber())->isEqualTo(1)
			->integer($otherScore->getMemoryUsageNumber())->isEqualTo(1)
			->object($score->merge($otherScore))->isIdenticalTo($score)
			->integer($score->getPassNumber())->isEqualTo(2)
			->integer($score->getFailNumber())->isEqualTo(2)
			->integer($score->getExceptionNumber())->isEqualTo(2)
			->integer($score->getErrorNumber())->isEqualTo(2)
			->integer($score->getOutputNumber())->isEqualTo(2)
			->integer($score->getDurationNumber())->isEqualTo(2)
			->integer($score->getMemoryUsageNumber())->isEqualTo(2)
			->object($score->merge($otherScore))->isIdenticalTo($score)
			->integer($score->getPassNumber())->isEqualTo(3)
			->integer($score->getFailNumber())->isEqualTo(3)
			->integer($score->getExceptionNumber())->isEqualTo(3)
			->integer($score->getErrorNumber())->isEqualTo(3)
			->integer($score->getOutputNumber())->isEqualTo(3)
			->integer($score->getDurationNumber())->isEqualTo(3)
			->integer($score->getMemoryUsageNumber())->isEqualTo(3)
		;
	}

	public function testErrorExists()
	{
		$score = new atoum\score();

		$this->assert
			->boolean($score->errorExists(uniqid()))->isFalse()
			->boolean($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isFalse()
		;

		$type = rand(1, PHP_INT_MAX - 1);
		$message = uniqid();

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $message);

		$this->assert
			->variable($score->errorExists(uniqid()))->isNull()
			->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
			->integer($score->errorExists($message))->isEqualTo(0)
			->integer($score->errorExists(null, $type))->isEqualTo(0)
			->integer($score->errorExists($message, $type))->isEqualTo(0)
		;

		$otherMessage = uniqid();

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $otherMessage);

		$this->assert
			->variable($score->errorExists(uniqid()))->isNull()
			->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
			->integer($score->errorExists($message))->isEqualTo(0)
			->integer($score->errorExists(null, $type))->isEqualTo(0)
			->integer($score->errorExists($message, $type))->isEqualTo(0)
			->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
		;

		$otherType = $type + 1;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $otherType, $message);

		$this->assert
			->variable($score->errorExists(uniqid()))->isNull()
			->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
			->integer($score->errorExists($message))->isEqualTo(0)
			->integer($score->errorExists(null, $type))->isEqualTo(0)
			->integer($score->errorExists($message, $type))->isEqualTo(0)
			->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
			->variable($score->errorExists($otherMessage, $otherType))->isNull()
			->integer($score->errorExists($message, $otherType))->isEqualTo(2)
			->integer($score->errorExists(null, $otherType))->isEqualTo(2)
		;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $otherType, $otherMessage);

		$this->assert
			->variable($score->errorExists(uniqid()))->isNull()
			->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
			->integer($score->errorExists($message))->isEqualTo(0)
			->integer($score->errorExists(null, $type))->isEqualTo(0)
			->integer($score->errorExists($message, $type))->isEqualTo(0)
			->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
			->integer($score->errorExists($otherMessage, $otherType))->isEqualTo(3)
			->integer($score->errorExists($message, $otherType))->isEqualTo(2)
			->integer($score->errorExists(null, $otherType))->isEqualTo(2)
		;
	}

	public function testDeleteError()
	{
		$score = new atoum\score();

		$key = rand(- PHP_INT_MAX, PHP_INT_MAX);

		$exception = null;

		try
		{
			$score->deleteError($key);
		}
		catch (\exception $exception) {}

		$this->assert
			->exception($exception)
				->isInstanceOf('\runtimeException')
				->hasMessage('Error key \'' . $key . '\' does not exist')
		;

		$message = uniqid();
		$type = rand(1, PHP_INT_MAX);

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $message);

		$this->assert
			->integer($score->errorExists($message, $type))->isEqualTo(0)
			->object($score->deleteError(0))->isIdenticalTo($score)
			->variable($score->errorExists($message, $type))->isNull()
		;
	}
}

?>
