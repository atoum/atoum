<?php

namespace mageekguy\atoum\tests\units\test\engines;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\engines\concurrent as testedClass
;

class concurrent extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\test\engine');
	}

	public function test__construct()
	{
		$this
			->if($engine = new testedClass())
			->then
				->object($defaultScoreFactory = $engine->getScoreFactory())->isInstanceOf('closure')
				->object($defaultScoreFactory())->isInstanceOf('mageekguy\atoum\score')
				->object($defaultPhpFactory = $engine->getPhpFactory())->isInstanceOf('closure')
				->object($defaultPhpFactory())->isInstanceOf('mageekguy\atoum\php')
				->object($engine->getPhp())->isEqualTo(new atoum\php())
		;
	}

	public function testSetPhp()
	{
		$this
			->if($engine = new testedClass())
			->then
				->object($engine->setPhp($php = new atoum\php()))->isIdenticalTo($engine)
				->object($engine->getPhp())->isIdenticalTo($php)
				->object($engine->setPhp())->isIdenticalTo($engine)
				->object($engine->getPhp())
					->isEqualTo(new atoum\php())
					->isNotIdenticalTo($php)
		;
	}

	public function testIsAsynchronous()
	{
		$this
			->if($engine = new testedClass())
			->then
				->boolean($engine->isAsynchronous())->isTrue()
		;
	}

	public function testRun()
	{
		$self = $this;

		$this
			->if($engine = new testedClass())
			->and($engine->setPhpFactory(function() use (& $php) {
					return $php = new \mock\mageekguy\atoum\php();
				}
			))
			->then
				->object($engine->run($test = new \mock\mageekguy\atoum\test()))->isIdenticalTo($engine)
			->if($test->getMockController()->getCurrentMethod = $method = uniqid())
			->and($test->getMockController()->getPath = $testPath = uniqid())
			->and($test->getMockController()->getPhpPath = $phpPath = uniqid())
			->and($test->getMockController()->codeCoverageIsEnabled = false)
			->and($test->getMockController()->getBootstrapFile = null)
			->and($test->setXdebugConfig($xdebugConfig = uniqid()))
			->and($engine->setPhpFactory(function() use (& $php, & $exception, $self) {
					$php = new \mock\mageekguy\atoum\php();
					$self->calling($php)->run->throw = $exception = new atoum\php\exception();

					return $php;
				}
			))
			->and($this->function->getenv = false)
			->and($this->function->ini_get = 0)
			->then
				->exception(function() use ($engine, $test) { $engine->run($test); })
					->isIdenticalTo($exception)
			->if($engine->setPhpFactory(function() use (& $php, $self) {
					$php = new \mock\mageekguy\atoum\php();
					$self->calling($php)->run = $php;

					return $php;
				}
			))
			->then
				->object($engine->run($test))->isIdenticalTo($engine)
				->mock($php)
					->call('run')->withArguments(
						'<?php ' .
						'ob_start();' .
						'require \'' . atoum\directory . '/classes/autoloader.php\';' .
						'require \'' . $testPath . '\';' .
						'$test = new ' . get_class($test) . '();' .
						'$test->setLocale(new ' . get_class($test->getLocale()) . '(' . $test->getLocale()->get() . '));' .
						'$test->setPhpPath(\'' . $phpPath . '\');' .
						'$test->disableCodeCoverage();' .
						'ob_end_clean();' .
						'mageekguy\atoum\scripts\runner::disableAutorun();' .
						'echo serialize($test->runTestMethod(\'' . $method . '\')->getScore());'
					)->once()
					->call('__set')->withArguments('XDEBUG_CONFIG', $xdebugConfig)->once()
		;
	}

	public function testGetScore()
	{
		$self = $this;

		$this
			->if($engine = new testedClass())
			->and($engine->setPhpFactory(function() use (& $php, $self) {
				$php = new \mock\mageekguy\atoum\php();
				$self->calling($php)->run = $php;
				$self->calling($php)->isRunning = false;

				return $php;
			}))
			->then
				->variable($engine->getScore())->isNull()
			->if($engine->setPhpFactory(function() use (& $php, $self) {
				$php = new \mock\mageekguy\atoum\php();
				$self->calling($php)->run = $php;
				$self->calling($php)->isRunning = true;

				return $php;
			}))
			->and($engine->run($test = new \mock\mageekguy\atoum\test()))
			->then
				->variable($engine->getScore())->isNull()
			->if( $this->calling($test)->getCurrentMethod = $method = uniqid())
			->and($this->calling($test)->getPath = $testPath = uniqid())
			->and($this->calling($test)->getPhpPath = $phpPath = uniqid())
			->and($this->calling($test)->codeCoverageIsEnabled = false)
			->and($this->calling($test)->getBootstrapFile = null)
			->and($engine->setPhpFactory(function() use (& $php, & $output, & $exitCode, $self) {
				$php = new \mock\mageekguy\atoum\php();
				$self->calling($php)->run = $php;
				$self->calling($php)->isRunning = false;
				$self->calling($php)->getStdOut = $output = uniqid();
				$self->calling($php)->getExitCode = $exitCode = uniqid();

				return $php;
			}))
			->and($engine->run($test))
			->then
				->object($score = $engine->getScore())->isInstanceOf('mageekguy\atoum\score')
				->array($score->getUncompletedMethods())->isEqualTo(array(array('file' => $testPath, 'class' => get_class($test), 'method' => $method, 'exitCode' => $exitCode, 'output' => $output)))
			->if($engine->setPhpFactory(function() use (& $php, $score, $self) {
				$php = new \mock\mageekguy\atoum\php();
				$self->calling($php)->run = $php;
				$self->calling($php)->isRunning = false;
				$self->calling($php)->getStdOut = serialize($score);
				$self->calling($php)->getExitCode = $exitCode = uniqid();

				return $php;
			}))
			->and($engine->run($test))
			->then
				->object($score = $engine->getScore())->isEqualTo($score)
		;
	}
}
