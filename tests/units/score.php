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
			->variable($score->getTestClass())->isNull()
			->collection($score->getPassAssertions())->isEmpty()
			->collection($score->getFailAssertions())->isEmpty()
			->collection($score->getExceptions())->isEmpty()
			->collection($score->getErrors())->isEmpty()
			->collection($score->getOutputs())->isEmpty()
			->collection($score->getDurations())->isEmpty()
			->collection($score->getMemoryUsages())->isEmpty()
		;

		$score = new atoum\score($this);

		$this->assert
			->string($score->getTestClass())->isEqualTo(__CLASS__)
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
						'value' => $output . $moreOutput
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
						'value' => $duration + $moreDuration
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
						'value' => $memoryUsage + $moreMemoryUsage
					),
					array(
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherMemoryUsage
					)
				)
			)
		;
	}

	public function testSetTestClass()
	{
		$score = new atoum\score();

		$this->assert
			->object($score->setTestClass($this))->isIdenticalTo($score)
			->string($score->getTestClass())->isEqualTo(__CLASS__)
			->collection($score->getPassAssertions())->isEmpty()
			->collection($score->getFailAssertions())->isEmpty()
			->collection($score->getExceptions())->isEmpty()
			->collection($score->getErrors())->isEmpty()
			->collection($score->getOutputs())->isEmpty()
			->collection($score->getDurations())->isEmpty()
			->collection($score->getMemoryUsages())->isEmpty()
		;
	}
}

?>
