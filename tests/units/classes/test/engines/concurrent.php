<?php

namespace mageekguy\atoum\tests\units\test\engines;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\engines
;

class concurrent extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\test\engine');
	}

	public function test__construct()
	{
		$this
			->if($engine = new engines\concurrent())
			->then
				->boolean($engine->isRunning())->isFalse()
		;
	}

	public function testIsAsynchronous()
	{
		$this
			->if($engine = new engines\concurrent())
			->then
				->boolean($engine->isAsynchronous())->isTrue()
		;
	}

	public function testRun()
	{
		$this
			->if($factory = new atoum\factory())
			->and($factory['mageekguy\atoum\adapter'] = $adapter = new atoum\test\adapter())
			->and($engine = new engines\concurrent($factory))
			->then
				->object($engine->run($test = new \mock\mageekguy\atoum\test()))->isIdenticalTo($engine)
				->boolean($engine->isRunning())->isFalse()
			->if($test->getMockController()->getCurrentMethod = $method = uniqid())
			->and($test->getMockController()->getPath = $testPath = uniqid())
			->and($test->getMockController()->getPhpPath = $phpPath = uniqid())
			->and($test->getMockController()->codeCoverageIsEnabled = false)
			->and($test->getMockController()->getBootstrapFile = null)
			->and($adapter->proc_open = false)
			->then
				->exception(function() use ($engine, $test) { $engine->run($test); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to use \'' . $phpPath . '\'')
			->if($adapter->proc_open = function($command, array $descriptors, array & $pipes) use (& $resource, & $stdIn, & $stdOut, & $stdErr) {
					$pipes = array(
						$stdIn = uniqid(),
						$stdOut = uniqid(),
						$stdErr = uniqid()
					);

					return $resource = uniqid();
				}
			)
			->and($adapter->stream_set_blocking = function() {})
			->and($adapter->fwrite = function() {})
			->and($adapter->fclose = function() {})
			->then
				->object($engine->run($test))->isIdenticalTo($engine)
				->boolean($engine->isRunning())->isTrue()
				->adapter($adapter)
					->call('fwrite')->withArguments(
						$stdIn,
						'<?php ' .
						'define(\'mageekguy\atoum\autorun\', false);' .
						'require \'' . atoum\directory . '/scripts/runner.php\';' .
						'require \'' . $testPath . '\';' .
						'$test = new ' . get_class($test) . '();' .
						'$test->setLocale(new ' . get_class($test->getLocale()) . '(' . $test->getLocale()->get() . '));' .
						'$test->setPhpPath(\'' . $phpPath . '\');' .
						'$test->disableCodeCoverage();' .
						'echo serialize($test->runTestMethod(\'' . $method . '\')->getScore());'
					)
					->call('fclose')->withArguments($stdIn)
		;
	}

	public function testGetScore()
	{
		$this
			->if($engine = new engines\concurrent())
			->then
				->variable($engine->getScore())->isNull()
			->if($factory = new atoum\factory())
			->and($factory['mageekguy\atoum\adapter'] = $adapter = new atoum\test\adapter())
			->and($engine = new engines\concurrent($factory))
			->and($engine->run($test = new \mock\mageekguy\atoum\test()))
			->then
				->variable($engine->getScore())->isNull()
			->if($test->getMockController()->getCurrentMethod = $method = uniqid())
			->and($test->getMockController()->getPath = $testPath = uniqid())
			->and($test->getMockController()->getPhpPath = $phpPath = uniqid())
			->and($test->getMockController()->codeCoverageIsEnabled = false)
			->and($test->getMockController()->getBootstrapFile = null)
			->and($adapter->proc_open = function($command, array $descriptors, array & $pipes) use (& $resource, & $stdIn, & $stdOut, & $stdErr) {
					$pipes = array(
						$stdIn = uniqid(),
						$stdOut = uniqid(),
						$stdErr = uniqid()
					);

					return $resource = uniqid();
				}
			)
			->and($adapter->proc_close = function() {})
			->and($adapter->proc_get_status = array('running' => true))
			->and($adapter->fwrite = function() {})
			->and($adapter->fclose = function() {})
			->and($adapter->stream_set_blocking = function() {})
			->and($adapter->stream_get_contents = $output = uniqid())
			->and($engine->run($test))
			->then
				->variable($engine->getScore())->isNull()
			->if($adapter->proc_get_status = array('running' => false, 'exitcode' => $exitCode = rand(1, PHP_INT_MAX)))
			->then
				->object($score = $engine->getScore())->isInstanceOf('mageekguy\atoum\score')
				->array($score->getUncompletedMethods())->isEqualTo(array(array('class' => get_class($test), 'method' => $method, 'exitCode' => $exitCode, 'output' => $output . $output)))
			->if($adapter->stream_get_contents = serialize($score))
			->and($engine->run($test))
			->then
				->object($score = $engine->getScore())->isEqualTo($score)
		;
	}
}
