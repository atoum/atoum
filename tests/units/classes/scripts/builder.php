<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\scripts,
	mageekguy\atoum\scripts\builder\vcs,
	mageekguy\atoum\scripts\builder as testedClass
;

require_once __DIR__ . '/../../runner.php';

class builder extends atoum\test
{
	public function beforeTestMethod($testMethod)
	{
		if (extension_loaded('svn') === false)
		{
			define('SVN_REVISION_HEAD', -1);
			define('PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS', 1);
			define('SVN_AUTH_PARAM_DEFAULT_USERNAME', 2);
			define('SVN_AUTH_PARAM_DEFAULT_PASSWORD', 3);
		}
	}

	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\script\configurable');
	}

	public function testClassConstants()
	{
		$this
			->string(scripts\builder::defaultUnitTestRunnerScript)->isEqualTo('scripts/runner.php')
			->string(scripts\builder::defaultPharGeneratorScript)->isEqualTo('scripts/phar/generator.php')
		;
	}

	public function test__construct()
	{
		$this
			->if($builder = new testedClass($name = uniqid()))
			->then
				->string($builder->getName())->isEqualTo($name)
				->object($builder->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($builder->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($builder->getArgumentsParser())->isInstanceOf('mageekguy\atoum\script\arguments\parser')
				->object($builder->getOutputWriter())->isInstanceOf('mageekguy\atoum\writers\std\out')
				->object($builder->getErrorWriter())->isInstanceOf('mageekguy\atoum\writers\std\err')
				->array($builder->getRunnerConfigurationFiles())->isEmpty()
				->variable($builder->getVersion())->isNull()
				->variable($builder->getWorkingDirectory())->isNull()
				->variable($builder->getDestinationDirectory())->isNull()
				->variable($builder->getErrorsDirectory())->isNull()
				->variable($builder->getScoreDirectory())->isNull()
				->variable($builder->getRevisionFile())->isNull()
				->string($builder->getUnitTestRunnerScript())->isEqualTo(scripts\builder::defaultUnitTestRunnerScript)
				->string($builder->getPharGeneratorScript())->isEqualTo(scripts\builder::defaultPharGeneratorScript)
				->variable($builder->getReportTitle())->isNull()
				->object($builder->getVcs())->isInstanceOf('mageekguy\atoum\scripts\builder\vcs\svn')
				->variable($builder->getTaggerEngine())->isNull()
			->if($builder = new testedClass($name = uniqid(), $adapter = new atoum\adapter()))
			->then
				->string($builder->getName())->isEqualTo($name)
				->object($builder->getAdapter())->isIdenticalTo($adapter)
				->object($builder->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($builder->getArgumentsParser())->isInstanceOf('mageekguy\atoum\script\arguments\parser')
				->object($builder->getOutputWriter())->isInstanceOf('mageekguy\atoum\writers\std\out')
				->object($builder->getErrorWriter())->isInstanceOf('mageekguy\atoum\writers\std\err')
				->array($builder->getRunnerConfigurationFiles())->isEmpty()
				->variable($builder->getVersion())->isNull()
				->variable($builder->getWorkingDirectory())->isNull()
				->variable($builder->getDestinationDirectory())->isNull()
				->variable($builder->getErrorsDirectory())->isNull()
				->variable($builder->getScoreDirectory())->isNull()
				->variable($builder->getRevisionFile())->isNull()
				->string($builder->getUnitTestRunnerScript())->isEqualTo(scripts\builder::defaultUnitTestRunnerScript)
				->string($builder->getPharGeneratorScript())->isEqualTo(scripts\builder::defaultPharGeneratorScript)
				->variable($builder->getReportTitle())->isNull()
				->object($builder->getVcs())->isInstanceOf('mageekguy\atoum\scripts\builder\vcs\svn')
				->variable($builder->getTaggerEngine())->isNull()
		;
	}

	public function testSetPhp()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setPhp($php = new atoum\php()))->isIdenticalTo($builder)
				->object($builder->getPhp())->isIdenticalTo($php)
				->object($builder->setPhp())->isIdenticalTo($builder)
				->object($builder->getPhp())
					->isEqualTo(new atoum\php())
					->isNotIdenticalTo($php)
		;
	}

	public function testSetVersion()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setVersion($tag = uniqid()))->isIdenticalTo($builder)
				->string($builder->getVersion())->isIdenticalTo($tag)
				->object($builder->setVersion($tag = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getVersion())->isIdenticalTo((string) $tag)
		;
	}

	public function testGetPhpPath()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->string($builder->getPhpPath())->isEqualTo($builder->getPhp()->getBinaryPath())
		;
	}

	public function testSetPhpPath()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setPhpPath($phpPath = uniqid()))->isIdenticalTo($builder)
				->string($builder->getPhpPath())->isIdenticalTo($phpPath)
		;
	}

	public function testSetReportTitle()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setReportTitle($reportTitle = uniqid()))->isIdenticalTo($builder)
				->string($builder->getReportTitle())->isEqualTo($reportTitle)
				->object($builder->setReportTitle($reportTitle = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getReportTitle())->isEqualTo((string) $reportTitle)
		;
	}

	public function testSetVcs()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->and->mockGenerator->shunt('__construct')
			->then
				->object($builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs()))->isIdenticalTo($builder)
				->object($builder->getVcs())->isIdenticalTo($vcs)
		;
	}

	public function testSetTaggerEngine()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setTaggerEngine($taggerEngine = new atoum\scripts\tagger\engine()))->isIdenticalTo($builder)
				->object($builder->getTaggerEngine())->isIdenticalTo($taggerEngine)
		;
	}

	public function testSetUnitTestRunnerScript()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setUnitTestRunnerScript($php = uniqid()))->isIdenticalTo($builder)
				->string($builder->getUnitTestRunnerScript())->isIdenticalTo($php)
				->object($builder->setUnitTestRunnerScript($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getUnitTestRunnerScript())->isIdenticalTo((string) $php)
		;
	}

	public function testSetPharGeneratorScript()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setPharGeneratorScript($php = uniqid()))->isIdenticalTo($builder)
				->string($builder->getPharGeneratorScript())->isIdenticalTo($php)
				->object($builder->setPharGeneratorScript($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getPharGeneratorScript())->isIdenticalTo((string) $php)
		;
	}

	public function testSetScoreDirectory()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setScoreDirectory($scoreDirectory = uniqid()))->isIdenticalTo($builder)
				->string($builder->getScoreDirectory())->isEqualTo($scoreDirectory)
				->object($builder->setScoreDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getScoreDirectory())->isEqualTo($directory)
				->object($builder->setScoreDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($builder)
				->string($builder->getScoreDirectory())->isEqualTo($directory)
		;
	}

	public function testSetErrorsDirectory()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setErrorsDirectory($errorsDirectory = uniqid()))->isIdenticalTo($builder)
				->string($builder->getErrorsDirectory())->isEqualTo($errorsDirectory)
				->object($builder->setErrorsDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getErrorsDirectory())->isEqualTo($directory)
				->object($builder->setErrorsDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($builder)
				->string($builder->getErrorsDirectory())->isEqualTo($directory)
		;
	}

	public function testSetDestinationDirectory()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($builder)
				->string($builder->getDestinationDirectory())->isEqualTo($directory)
				->object($builder->setDestinationDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getDestinationDirectory())->isEqualTo($directory)
				->object($builder->setDestinationDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($builder)
				->string($builder->getDestinationDirectory())->isEqualTo($directory)
		;
	}

	public function testSetWorkingDirectory()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setWorkingDirectory($directory = uniqid()))->isIdenticalTo($builder)
				->string($builder->getWorkingDirectory())->isEqualTo($directory)
				->object($builder->setWorkingDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
				->string($builder->getWorkingDirectory())->isEqualTo((string) $directory)
				->object($builder->setWorkingDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($builder)
				->string($builder->getWorkingDirectory())->isEqualTo($directory)
		;
	}

	public function testSetRevisionFile()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setRevisionFile($file = uniqid()))->isIdenticalTo($builder)
				->string($builder->getRevisionFile())->isEqualTo($file)
		;
	}

	public function testAddRunnerConfigurationFile()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->addRunnerConfigurationFile($file = uniqid()))->isIdenticalTo($builder)
				->array($builder->getRunnerConfigurationFiles())->isEqualTo(array($file))
		;
	}

	public function testSetRunFile()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->object($builder->setRunFile($runFile = uniqid()))->isIdenticalTo($builder)
				->string($builder->getRunFile())->isEqualTo($runFile)
		;
	}

	public function testDisableUnitTestChecking()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->boolean($builder->unitTestCheckingIsEnabled())->isTrue()
				->object($builder->disableUnitTestChecking())->isIdenticalTo($builder)
				->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
		;
	}

	public function testEnableUnitTestChecking()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->and($builder->disableUnitTestChecking())
			->then
				->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
				->object($builder->enableUnitTestChecking())->isIdenticalTo($builder)
				->boolean($builder->unitTestCheckingIsEnabled())->isTrue()
		;
	}

	public function testCheckUnitTests()
	{
		$this
			->if($builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $adapter = new atoum\test\adapter()))
			->and($builder->disableUnitTestChecking())
			->then
				->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
				->boolean($builder->checkUnitTests())->isTrue()
			->if($builder->enableUnitTestChecking())
			->then
				->exception(function() use ($builder) {
						$builder->checkUnitTests();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to check unit tests, working directory is undefined')
			->if->mockGenerator->shunt('__construct')
			->and($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs())
			->and($this->calling($vcs)->exportRepository = function() {})
			->and($builder->setVcs($vcs))
			->and($php = new \mock\mageekguy\atoum\php())
			->and($this->calling($php)->run = $php)
			->and($builder->setPhp($php))
			->and($builder->setWorkingDirectory($workingDirectory = uniqid()))
			->and($builder->setUnitTestRunnerScript($unitTestRunnerScript = uniqid()))
			->and($builder->setReportTitle($reportTitle = uniqid()))
			->and($builder->addRunnerConfigurationFile($runnerConfigurationFile = uniqid()))
			->and($score = new \mock\mageekguy\atoum\score())
			->and($this->calling($score)->getFailNumber = 0)
			->and($this->calling($score)->getExceptionNumber = 0)
			->and($this->calling($score)->getErrorNumber = 0)
			->and($adapter->sys_get_temp_dir = $tempDirectory = uniqid())
			->and($adapter->tempnam = $scoreFile = uniqid())
			->and($adapter->file_get_contents = $scoreFileContents = uniqid())
			->and($adapter->unserialize = $score)
			->and($adapter->unlink = true)
			->and($this->calling($builder)->writeErrorInErrorsDirectory = function() {})
			->then
				->boolean($builder->checkUnitTests())->isTrue()
				->mock($vcs)
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
				->adapter($adapter)
					->call('sys_get_temp_dir')->once()
					->call('tempnam')->withArguments($tempDirectory, '')->once()
					->call('file_get_contents')->withArguments($scoreFile)->once()
					->call('unserialize')->withArguments($scoreFileContents)->once()
					->call('unlink')->withArguments($scoreFile)->once()
			->mock($score)
				->call('getFailNumber')->once()
				->call('getExceptionNumber')->once()
				->call('getErrorNumber')->once()
			->mock($php)
					->call('reset')
						->before($this->mock($php)->call('run'))
							->once()
					->call('addOption')->withArguments('-f', $workingDirectory . \DIRECTORY_SEPARATOR . $unitTestRunnerScript)
						->before($this->mock($php)->call('run'))
							->once()
					->call('addArgument')->withArguments('-ncc')
						->before($this->mock($php)->call('run'))
							->once()
					->call('addArgument')->withArguments('-d', $workingDirectory . \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR . 'units' . \DIRECTORY_SEPARATOR . 'classes')
						->before($this->mock($php)->call('run'))
							->once()
					->call('addArgument')->withArguments('-p', $php->getBinaryPath())
						->before($this->mock($php)->call('run'))
							->once()
					->call('addArgument')->withArguments('-sf', $scoreFile)
						->before($this->mock($php)->call('run'))
							->once()
					->call('addArgument')->withArguments('-c', $runnerConfigurationFile)
						->before($this->mock($php)->call('run'))
							->once()
			->if($this->calling($php)->getExitCode = 127)
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments('Unable to find \'' . $php->getBinaryPath() . '\' or it is not executable')->once()
			->if($this->calling($php)->getExitCode = $exitCode = rand(1, 125))
			->and($this->calling($php)->getStdErr = $stdErr = uniqid())
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments($php . ' failed with exit code \'' . $exitCode . '\': ' . $stdErr)->once()
			->if($this->calling($php)->getExitCode = $exitCode = rand(128, PHP_INT_MAX))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments($php . ' failed with exit code \'' . $exitCode . '\': ' . $stdErr)->once()
			->if($this->calling($php)->getExitCode = 0)
			->and($this->calling($php)->getStdErr = '')
			->then
				->boolean($builder->checkUnitTests())->isTrue()
			->if($this->calling($php)->getStdErr = $stdErrContents = uniqid())
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments($stdErrContents)->once()
			->if($this->calling($php)->getStdErr = '')
			->and($adapter->file_get_contents = false)
			->and($this->resetMock($builder))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments('Unable to read score from file \'' . $scoreFile . '\'')->once()
			->if($adapter->file_get_contents = $scoreFileContents)
			->and($adapter->unserialize = false)
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments('Unable to unserialize score from file \'' . $scoreFile . '\'')->once()
			->if($adapter->unserialize = uniqid())
			->then
				->boolean($builder->checkUnitTests())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments('Contents of file \'' . $scoreFile . '\' is not a score')->once()
			->if($adapter->unserialize = $score)
			->and($adapter->unlink = false)
			->then
				->exception(function() use ($builder) {
						$builder->checkUnitTests();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to delete score file \'' . $scoreFile . '\'')
			->if($adapter->unlink = true)
			->and($this->calling($score)->getFailNumber = rand(1, PHP_INT_MAX))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
			->if($this->calling($score)->getFailNumber = 0)
			->and($this->calling($score)->getExceptionNumber = rand(1, PHP_INT_MAX))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
			->if($this->calling($score)->getExceptionNumber = 0)
			->and($this->calling($score)->getErrorNumber = rand(1, PHP_INT_MAX))
			->then
				->boolean($builder->checkUnitTests())->isFalse()
		;
	}

	public function testDisablePharCreation()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->then
				->boolean($builder->pharCreationIsEnabled())->isTrue()
				->object($builder->disablePharCreation())->isIdenticalTo($builder)
				->boolean($builder->pharCreationIsEnabled())->isFalse()
		;
	}

	public function testEnablePharCreation()
	{
		$this
			->if($builder = new testedClass(uniqid()))
			->and($builder->disablePharCreation())
			->then
				->boolean($builder->pharCreationIsEnabled())->isFalse()
				->object($builder->enablePharCreation())->isIdenticalTo($builder)
				->boolean($builder->pharCreationIsEnabled())->isTrue()
		;
	}

	public function testCreatePhar()
	{
		$this
			->if($builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $adapter = new atoum\test\adapter()))
			->and($builder->setTaggerEngine($taggerEngine = new \mock\mageekguy\atoum\scripts\tagger\engine()))
			->and($this->calling($taggerEngine)->tagVersion = function() {})
			->and($builder->disablePharCreation())
			->then
				->boolean($builder->createPhar())->isTrue()
			->if($builder->enablePharCreation())
			->and->mockGenerator->shunt('__construct')
			->and($builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs()))
			->and($this->calling($vcs)->getNextRevisions = array())
			->and($this->calling($vcs)->exportRepository = function() {})
			->then
				->exception(function() use ($builder) {
							$builder->createPhar();
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to create phar, destination directory is undefined')
			->if($builder->setDestinationDirectory($destinationDirectory = uniqid()))
			->then
				->exception(function() use ($builder) {
							$builder->createPhar();
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Unable to create phar, working directory is undefined')
			->if($builder->setWorkingDirectory($workingDirectory = uniqid()))
			->and($builder->setPhp($php = new \mock\mageekguy\atoum\php()))
			->and($this->calling($php)->run = $php)
			->and($builder->setPharGeneratorScript($pharGeneratorScript = uniqid()))
			->and($this->calling($builder)->writeErrorInErrorsDirectory = function() {})
			->and($adapter->file_get_contents = false)
			->then
				->boolean($builder->createPhar())->isTrue()
			->if($this->calling($vcs)->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->and($builder->disableUnitTestChecking())
			->and($this->calling($php)->getExitCode = rand(1, PHP_INT_MAX))
			->and($this->calling($php)->getStderr = $stderr = uniqid())
			->then
				->boolean($builder->createPhar())->isFalse()
				->mock($builder)
					->call('writeErrorInErrorsDirectory')->withArguments('Unable to run ' . $php . ': ' . $stderr)->once()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($this->calling($php)->getExitCode = 0)
			->and($adapter->date = $date = uniqid())
			->and($this->calling($vcs)->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->and($this->resetMock($vcs))
			->then
				->boolean($builder->createPhar())->isTrue()
				->mock($taggerEngine)
					->call('setVersion')
						->withArguments('nightly-' . $revision . '-' . $date)
						->once()
					->call('tagVersion')->atLeastOnce()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($this->resetMock($vcs))
			->and($this->resetMock($taggerEngine))
			->and($this->calling($vcs)->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->then
				->boolean($builder->createPhar($tag = uniqid()))->isTrue()
				->mock($taggerEngine)
					->call('setVersion')->withArguments($tag)->once()
					->call('tagVersion')->once()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($builder->setRevisionFile($revisionFile = uniqid()))
			->and($adapter->file_get_contents = false)
			->and($adapter->file_put_contents = function() {})
			->and($this->resetMock($vcs))
			->and($this->calling($vcs)->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->then
				->boolean($builder->createPhar())->isTrue()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($revisionFile)->once()
					->call('file_put_contents')->withArguments($revisionFile, $revision, \LOCK_EX)->once()
				->mock($vcs)
					->call('setRevision')->withArguments($revision)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
			->if($this->resetMock($vcs))
			->and($this->calling($vcs)->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); })
			->and($adapter->file_put_contents = false)
			->then
				->exception(function() use ($builder) {
							$builder->createPhar();
						}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to save last revision in file \'' . $revisionFile . '\'')
			->if($this->resetMock($vcs))
			->and($this->calling($vcs)->getNextRevisions[1] = array(1, 2, 3))
			->and($this->calling($vcs)->getNextRevisions[2] = array(2, 3))
			->and($this->calling($vcs)->getNextRevisions[3] = array(3))
			->and($this->calling($vcs)->getNextRevisions[4] = array())
			->and($adapter->file_put_contents = function() {})
			->and($adapter->resetCalls())
			->then
				->boolean($builder->createPhar())->isTrue()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($revisionFile)->once()
					->call('file_put_contents')->withArguments($revisionFile, 3, \LOCK_EX)->once()
				->mock($vcs)
					->call('setRevision')->withArguments(1)->once()
					->call('setRevision')->withArguments(2)->once()
					->call('setRevision')->withArguments(3)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->atLeastOnce()
					->call('exportRepository')->atLeastOnce()
			->if($this->resetMock($vcs))
			->and($this->calling($vcs)->getNextRevisions[1] = array(4))
			->and($this->calling($vcs)->getNextRevisions[2] = array())
			->and($adapter->file_get_contents = 1)
			->and($adapter->resetCalls())
			->then
				->boolean($builder->createPhar())->isTrue()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($revisionFile)->once()
					->call('file_put_contents')->withArguments($revisionFile, 4, \LOCK_EX)->once()
				->mock($vcs)
					->call('setRevision')->withArguments(4)->once()
					->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
					->call('exportRepository')->once()
		;
	}

	public function testRun()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->file_get_contents = false)
			->and($adapter->fopen = $runFileResource = uniqid())
			->and($adapter->flock = true)
			->and($adapter->getmypid = $pid = uniqid())
			->and($adapter->fwrite = function() {})
			->and($adapter->fclose = function() {})
			->and($adapter->unlink = function() {})
			->and($builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $adapter))
			->and($builder->setRunFile($runFile = uniqid()))
			->and($this->calling($builder)->createPhar = function() {})
			->then
				->object($builder->run())->isIdenticalTo($builder)
				->mock($builder)->call('createPhar')->once()
				->adapter($adapter)
					->call('file_get_contents')->withArguments($runFile)->once()
					->call('fopen')->withArguments($runFile, 'w+')->once()
					->call('flock')->withArguments($runFileResource, \LOCK_EX | \LOCK_NB)->once()
					->call('fwrite')->withArguments($runFileResource, $pid)->once()
					->call('fclose')->withArguments($runFileResource)->once()
					->call('unlink')->withArguments($runFile)->once()
		;
	}

	public function testLockRedmond()
	{
		$this
			->if($adapterRedmond = new atoum\test\adapter())
			->and($adapterRedmond->file_get_contents = '1')
			->and($adapterRedmond->function_exists = false)
			->and($builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $adapterRedmond))
			->and($builder->setRunFile($runFile = uniqid()))
			->and($this->calling($builder)->createPhar = function() {})
			->then
					->exception(function() use ($builder) {
							$builder->run();
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage(sprintf('A process has locked run file \'%s\'', $runFile))
		;
	}

	public function testLockPosix()
	{
		$this
			->if($adapterPosix = new \mock\mageekguy\atoum\test\adapter())
			->and($adapterPosix->file_get_contents = '1')
			->and($adapterPosix->function_exists = true)
			->and($adapterPosix->posix_kill = false)
			->and($builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $adapterPosix))
			->and($builder->setRunFile($runFile = uniqid()))
			->and($this->calling($builder)->createPhar = function() {})
			->and($this->calling($builder)->createPhar = function() {})
			->then
				->object($builder->run())->isIdenticalTo($builder)
				->mock($builder)->call('createPhar')->once()
				->mock($adapterPosix)->call('posix_kill')->once()
		;
	}

	public function testWriteInErrorDirectory()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->file_put_contents = function() {})
			->and($builder = new testedClass(uniqid(), $adapter))
			->then
				->variable($builder->getErrorsDirectory())->isNull()
				->object($builder->writeErrorInErrorsDirectory(uniqid()))->isIdenticalTo($builder)
				->adapter($adapter)->call('file_put_contents')->never()
			->if($builder->setErrorsDirectory($errorDirectory = uniqid()))
			->then
				->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
				->exception(function() use ($builder) {
							$builder->writeErrorInErrorsDirectory(uniqid());
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\logic')
						->hasMessage('Revision is undefined')
				->adapter($adapter)->call('file_put_contents')->never()
			->if->mockGenerator->shunt('__construct')
			->and($builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs()))
			->and($vcs->setRevision($revision = rand(1, PHP_INT_MAX)))
			->then
				->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
				->object($builder->writeErrorInErrorsDirectory($message = uniqid()))->isIdenticalTo($builder)
				->adapter($adapter)->call('file_put_contents')->withArguments($errorDirectory . \DIRECTORY_SEPARATOR . $revision, $message, \LOCK_EX | \FILE_APPEND)->once()
			->if($adapter->resetCalls())
			->and($adapter->file_put_contents = false)
			->then
				->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
				->exception(function() use ($builder, & $message) {
							$builder->writeErrorInErrorsDirectory($message = uniqid());
						}
					)
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Unable to save error in file \'' . $errorDirectory . \DIRECTORY_SEPARATOR . $revision . '\'')
				->adapter($adapter)->call('file_put_contents')->withArguments($errorDirectory . \DIRECTORY_SEPARATOR . $revision, $message, \LOCK_EX | \FILE_APPEND)->once()
		;
	}
}
