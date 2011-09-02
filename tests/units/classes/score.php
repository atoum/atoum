<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter
;

require_once(__DIR__ . '/../runner.php');

class score extends atoum\test
{
	public function test__construct()
	{
		$score = new atoum\score();

		$this->assert
			->variable($score->getPhpPath())->isNull()
			->variable($score->getPhpVersion())->isNull()
			->variable($score->getAtoumPath())->isNull()
			->variable($score->getAtoumVersion())->isNull()
			->integer($score->getPassNumber())->isZero()
			->array($score->getFailAssertions())->isEmpty()
			->array($score->getExceptions())->isEmpty()
			->array($score->getErrors())->isEmpty()
			->array($score->getOutputs())->isEmpty()
			->array($score->getDurations())->isEmpty()
			->array($score->getMemoryUsages())->isEmpty()
			->object($score->getCoverage())->isInstanceOf('mageekguy\atoum\score\coverage')
		;

		$score = new atoum\score($coverage = new atoum\score\coverage());

		$this->assert
			->variable($score->getPhpPath())->isNull()
			->variable($score->getPhpVersion())->isNull()
			->variable($score->getAtoumPath())->isNull()
			->variable($score->getAtoumVersion())->isNull()
			->integer($score->getPassNumber())->isZero()
			->array($score->getFailAssertions())->isEmpty()
			->array($score->getExceptions())->isEmpty()
			->array($score->getErrors())->isEmpty()
			->array($score->getOutputs())->isEmpty()
			->array($score->getDurations())->isEmpty()
			->array($score->getMemoryUsages())->isEmpty()
			->object($score->getCoverage())->isIdenticalTo($coverage)
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
			->array($score->getExceptions())->isEmpty()
			->integer($score->getExceptionNumber())->isZero()
			->object($score->addException($file, $line, $class, $method, $exception))->isIdenticalTo($score)
			->array($score->getExceptions())->isEqualTo(array(
					array(
						'case' => null,
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
			->array($score->getExceptions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'value' => (string) $exception
					),
					array(
						'case' => null,
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

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->integer($score->getPassNumber())->isZero()
			->object($score->addPass())->isIdenticalTo($score)
			->integer($score->getPassNumber())->isEqualTo(1)
		;

		$otherFile = uniqid();
		$otherLine = rand(1, PHP_INT_MAX);
		$otherClass = uniqid();
		$otherMethod = uniqid();

		$this->assert
			->object($score->addPass())->isIdenticalTo($score)
			->integer($score->getPassNumber())->isEqualTo(2)
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
		$asserter = new atoum\asserters\integer(new asserter\generator(new self($score)));
		$reason = uniqid();

		$this->assert
			->array($score->getFailAssertions())->isEmpty()
			->integer($score->getFailNumber())->isZero()
			->integer($score->addFail($file, $line, $class, $method, $asserter, $reason))->isGreaterThan(0)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
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
		$otherAsserter = new atoum\asserters\integer(new asserter\generator(new self($score)));
		$otherReason = uniqid();

		$this->assert
			->integer($score->addFail($otherFile, $otherLine, $otherClass, $otherMethod, $otherAsserter, $otherReason))->isGreaterThan(0)
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'asserter' => $asserter,
						'fail' => $reason
					),
					array(
						'case' => null,
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
		$errorFile = uniqid();
		$errorLine = rand(1, PHP_INT_MAX);

		$this->assert
			->array($score->getErrors())->isEmpty()
			->integer($score->getErrorNumber())->isZero()
			->object($score->addError($file, $line, $class, $method, $type, $message, $errorFile, $errorLine))->isIdenticalTo($score)
			->array($score->getErrors())->isEqualTo(array(
					array(
						'case' => null,
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
		;

		$otherFile = uniqid();
		$otherLine = rand(1, PHP_INT_MAX);
		$otherClass = uniqid();
		$otherMethod = uniqid();
		$otherType = rand(1, PHP_INT_MAX);
		$otherMessage = uniqid();
		$otherErrorFile = uniqid();
		$otherErrorLine = rand(1, PHP_INT_MAX);

		$this->assert
			->object($score->addError($otherFile, $otherLine, $otherClass, $otherMethod, $otherType, $otherMessage, $otherErrorFile, $otherErrorLine))->isIdenticalTo($score)
			->array($score->getErrors())->isEqualTo(array(
					array(
						'case' => null,
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
		;

		$anAnotherMessage = uniqid();

		$this->assert
			->object($score->addError($file, $line, $class, $method, $type, $anAnotherMessage, $errorFile, $errorLine))->isIdenticalTo($score)
			->array($score->getErrors())->isEqualTo(array(
					array(
						'case' => null,
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
						'class' => $otherClass,
						'method' => $otherMethod,
						'file' => $otherFile,
						'line' => $otherLine,
						'type' => $otherType,
						'message' => $otherMessage,
						'errorFile' => $otherErrorFile,
						'errorLine' => $otherErrorLine
					),
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'file' => $file,
						'line' => $line,
						'type' => $type,
						'message' => $anAnotherMessage,
						'errorFile' => $errorFile,
						'errorLine' => $errorLine
					),
				)
			)
			->integer($score->getErrorNumber())->isEqualTo(3)
		;
	}

	public function testAddOutput()
	{
		$score = new atoum\score();

		$class = uniqid();
		$method = uniqid();
		$output = uniqid();

		$this->assert
			->array($score->getOutputs())->isEmpty()
			->integer($score->getOutputNumber())->isZero()
			->object($score->addOutput($class, $method, $output))->isIdenticalTo($score)
			->array($score->getOutputs())->isEqualTo(array(
					array(
						'case' => null,
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
			->array($score->getOutputs())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $output
					),
					array(
						'case' => null,
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
			->array($score->getOutputs())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $output
					),
					array(
						'case' => null,
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherOutput
					),
					array(
						'case' => null,
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
			->array($score->getDurations())->isEmpty()
			->integer($score->getDurationNumber())->isZero()
			->object($score->addDuration($class, $method, $duration))->isIdenticalTo($score)
			->array($score->getDurations())->isEqualTo(array(
					array(
						'case' => null,
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
			->array($score->getDurations())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $duration
					),
					array(
						'case' => null,
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
			->array($score->getDurations())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $duration
					),
					array(
						'case' => null,
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
			->array($score->getDurations())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $duration
					),
					array(
						'case' => null,
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
			->array($score->getDurations())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $duration
					),
					array(
						'case' => null,
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherDuration
					),
					array(
						'case' => null,
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
			->array($score->getMemoryUsages())->isEmpty()
			->object($score->addMemoryUsage($class, $method, $memoryUsage))->isIdenticalTo($score)
			->array($score->getMemoryUsages())->isEqualTo(array(
					array(
						'case' => null,
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
			->array($score->getMemoryUsages())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $memoryUsage
					),
					array(
						'case' => null,
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherMemoryUsage
					)
				)
			)
		;

		$this->assert
			->object($score->addMemoryUsage(uniqid(), uniqid(), 0))->isIdenticalTo($score)
			->array($score->getMemoryUsages())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $memoryUsage
					),
					array(
						'case' => null,
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherMemoryUsage
					)
				)
			)
		;

		$this->assert
			->object($score->addMemoryUsage(uniqid(), uniqid(), - rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
			->array($score->getMemoryUsages())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $memoryUsage
					),
					array(
						'case' => null,
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
			->array($score->getMemoryUsages())->isEqualTo(array(
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $memoryUsage
					),
					array(
						'case' => null,
						'class' => $otherClass,
						'method' => $otherMethod,
						'value' => $otherMemoryUsage
					),
					array(
						'case' => null,
						'class' => $class,
						'method' => $method,
						'value' => $moreMemoryUsage
					)
				)
			)
		;
	}

	public function testSetAtoumPath()
	{
		$score = new atoum\score();

		$this->assert
			->object($score->setAtoumPath($path = uniqid()))->isIdenticalTo($score)
			->string($score->getAtoumPath())->isEqualTo($path)
			->exception(function() use ($score) {
					$score->setAtoumPath(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Path of atoum is already set')
			->object($score->reset()->setAtoumPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
			->string($score->getAtoumPath())->isEqualTo((string) $path)
		;
	}

	public function testSetAtoumVersion()
	{
		$score = new atoum\score();

		$this->assert
			->object($score->setAtoumVersion($version = uniqid()))->isIdenticalTo($score)
			->string($score->getAtoumVersion())->isEqualTo($version)
			->exception(function() use ($score) {
					$score->setAtoumVersion(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Version of atoum is already set')
			->object($score->reset()->setAtoumVersion($version = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
			->string($score->getAtoumVersion())->isEqualTo((string) $version)
		;
	}

	public function testSetPhpPath()
	{
		$score = new atoum\score();

		$this->assert
			->object($score->setPhpPath($path = uniqid()))->isIdenticalTo($score)
			->string($score->getPhpPath())->isEqualTo($path)
			->exception(function() use ($score) {
					$score->setPhpPath(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('PHP path is already set')
			->object($score->reset()->setPhpPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
			->string($score->getPhpPath())->isEqualTo((string) $path)
		;
	}

	public function testSetPhpVersion()
	{
		$score = new atoum\score();

		$this->assert
			->object($score->setPhpVersion(\PHP_VERSION_ID))->isIdenticalTo($score)
			->string($score->getPhpVersion())->isEqualTo((string) \PHP_VERSION_ID)
			->exception(function() use ($score) {
					$score->setPhpVersion(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('PHP version is already set')
		;
	}

	public function testReset()
	{
		$score = new atoum\score();

		$this->assert
			->variable($score->getPhpPath())->isNull()
			->variable($score->getPhpVersion())->isNull()
			->variable($score->getAtoumPath())->isNull()
			->variable($score->getAtoumVersion())->isNull()
			->integer($score->getPassNumber())->isZero()
			->array($score->getFailAssertions())->isEmpty()
			->array($score->getExceptions())->isEmpty()
			->array($score->getErrors())->isEmpty()
			->array($score->getOutputs())->isEmpty()
			->array($score->getDurations())->isEmpty()
			->array($score->getMemoryUsages())->isEmpty()
			->object($score->reset())->isIdenticalTo($score)
			->variable($score->getPhpPath())->isNull()
			->variable($score->getPhpVersion())->isNull()
			->variable($score->getAtoumPath())->isNull()
			->variable($score->getAtoumVersion())->isNull()
			->integer($score->getPassNumber())->isZero()
			->array($score->getFailAssertions())->isEmpty()
			->array($score->getExceptions())->isEmpty()
			->array($score->getErrors())->isEmpty()
			->array($score->getOutputs())->isEmpty()
			->array($score->getDurations())->isEmpty()
			->array($score->getMemoryUsages())->isEmpty()
		;

		$score
			->setPhpPath(uniqid())
			->setPhpVersion(uniqid())
			->setAtoumPath(uniqid())
			->setAtoumVersion(uniqid())
			->addPass()
			->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception())
			->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX))
			->addOutput(uniqid(), uniqid(), uniqid())
			->addDuration(uniqid(), uniqid(), rand(1, PHP_INT_MAX))
			->addMemoryUsage(uniqid(), uniqid(), rand(1, PHP_INT_MAX))
		;

		$score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new asserter\generator(new self($score))), uniqid());

		$this->assert
			->variable($score->getPhpPath())->isNotNull()
			->variable($score->getPhpVersion())->isNotNull()
			->variable($score->getAtoumPath())->isNotNull()
			->variable($score->getAtoumVersion())->isNotNull()
			->integer($score->getPassNumber())->isGreaterThan(0)
			->array($score->getFailAssertions())->isNotEmpty()
			->array($score->getExceptions())->isNotEmpty()
			->array($score->getErrors())->isNotEmpty()
			->array($score->getOutputs())->isNotEmpty()
			->array($score->getDurations())->isNotEmpty()
			->array($score->getMemoryUsages())->isNotEmpty()
			->object($score->reset())->isIdenticalTo($score)
			->variable($score->getPhpPath())->isNull()
			->variable($score->getPhpVersion())->isNull()
			->variable($score->getAtoumPath())->isNull()
			->variable($score->getAtoumVersion())->isNull()
			->integer($score->getPassNumber())->isZero()
			->array($score->getFailAssertions())->isEmpty()
			->array($score->getExceptions())->isEmpty()
			->array($score->getErrors())->isEmpty()
			->array($score->getOutputs())->isEmpty()
			->array($score->getDurations())->isEmpty()
			->array($score->getMemoryUsages())->isEmpty()
		;
	}

	public function testMerge()
	{
		$score = new atoum\score();
		$otherScore = new atoum\score();

		$this->assert
			->integer($score->getPassNumber())->isZero()
			->array($score->getFailAssertions())->isEmpty()
			->array($score->getExceptions())->isEmpty()
			->array($score->getErrors())->isEmpty()
			->array($score->getOutputs())->isEmpty()
			->array($score->getDurations())->isEmpty()
			->array($score->getMemoryUsages())->isEmpty()
			->object($score->merge($otherScore))->isIdenticalTo($score)
			->integer($score->getPassNumber())->isZero()
			->array($score->getFailAssertions())->isEmpty()
			->array($score->getExceptions())->isEmpty()
			->array($score->getErrors())->isEmpty()
			->array($score->getOutputs())->isEmpty()
			->array($score->getDurations())->isEmpty()
			->array($score->getMemoryUsages())->isEmpty()
		;

		$score->addPass();
		$score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new asserter\generator(new self($score))), uniqid());
		$score->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception());
		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX));
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

		$otherScore->addPass();
		$otherScore->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new asserter\generator(new self($score))), uniqid());
		$otherScore->addException(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new \exception());
		$otherScore->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), E_ERROR, uniqid(), uniqid(), rand(1, PHP_INT_MAX));
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
			->variable($score->errorExists(uniqid()))->isNull()
			->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
		;

		$type = rand(1, PHP_INT_MAX - 1);
		$message = uniqid();

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $message, uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->variable($score->errorExists(uniqid()))->isNull()
			->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
			->integer($score->errorExists($message))->isEqualTo(0)
			->integer($score->errorExists(null, $type))->isEqualTo(0)
			->integer($score->errorExists($message, $type))->isEqualTo(0)
		;

		$otherMessage = uniqid();

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $otherMessage, uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->variable($score->errorExists(uniqid()))->isNull()
			->variable($score->errorExists(uniqid(), rand(1, PHP_INT_MAX)))->isNull()
			->integer($score->errorExists($message))->isEqualTo(0)
			->integer($score->errorExists(null, $type))->isEqualTo(0)
			->integer($score->errorExists($message, $type))->isEqualTo(0)
			->integer($score->errorExists($otherMessage, $type))->isEqualTo(1)
		;

		$otherType = $type + 1;

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $otherType, $message, uniqid(), rand(1, PHP_INT_MAX));

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

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $otherType, $otherMessage, uniqid(), rand(1, PHP_INT_MAX));

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
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Error key \'' . $key . '\' does not exist')
		;

		$message = uniqid();
		$type = rand(1, PHP_INT_MAX);

		$score->addError(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), $type, $message, uniqid(), rand(1, PHP_INT_MAX));

		$this->assert
			->integer($score->errorExists($message, $type))->isEqualTo(0)
			->object($score->deleteError(0))->isIdenticalTo($score)
			->variable($score->errorExists($message, $type))->isNull()
		;
	}

	public function testGetFailAssertions()
	{
		$score = new atoum\score();

		$this->assert
			->array($score->getFailAssertions())->isEmpty()
		;

		$score->addPass();

		$this->assert
			->array($score->getFailAssertions())->isEmpty()
		;

		$file = uniqid();
		$line = rand(1, PHP_INT_MAX);
		$class = uniqid();
		$method = uniqid();
		$asserter = new atoum\asserters\integer(new asserter\generator(new self($score)));
		$reason = uniqid();

		$score->addFail($file, $line, $class, $method, $asserter, $reason);

		$this->assert
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
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

	public function testGetPassAssertions()
	{
		$score = new atoum\score();

		$this->assert
			->integer($score->getPassNumber())->isZero()
		;

		$score->addFail(uniqid(), rand(1, PHP_INT_MAX), uniqid(), uniqid(), new atoum\asserters\integer(new asserter\generator(new self($score))), uniqid());

		$this->assert
			->integer($score->getPassNumber())->isZero()
		;

		$score->addPass();

		$this->assert
			->integer($score->getPassNumber())->isEqualTo(1)
		;
	}

	public function testGetCoverage()
	{
		$score = new atoum\score();

		$this->assert
			->object($score->getCoverage())->isInstanceOf('mageekguy\atoum\score\coverage')
		;
	}

	public function testSetCase()
	{
		$score = new atoum\score();

		$this->assert
			->object($score->setCase($case = uniqid()))->isIdenticalTo($score)
			->string($score->getCase())->isEqualTo($case)
			->object($score->setCase($case = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
			->string($score->getCase())->isEqualTo((string) $case)
		;
	}
}

?>
