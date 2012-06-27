<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\scripts,
	mageekguy\atoum\scripts\builder\vcs
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
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\script')
			->string(scripts\builder::defaultUnitTestRunnerScript)->isEqualTo('scripts/runner.php')
			->string(scripts\builder::defaultPharGeneratorScript)->isEqualTo('scripts/phar/generator.php')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($builder = new scripts\builder($name = uniqid()))
			->then
				->string($builder->getName())->isEqualTo($name)
				->object($builder->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($builder->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($builder->getArgumentsParser())->isInstanceOf('mageekguy\atoum\script\arguments\parser')
				->object($builder->getOutputWriter())->isInstanceOf('mageekguy\atoum\writers\std\out')
				->object($builder->getErrorWriter())->isInstanceOf('mageekguy\atoum\writers\std\err')
				->object($builder->getSuperglobals())->isInstanceOf('mageekguy\atoum\superglobals')
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
			->if($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory->returnWhenBuild('atoum\locale', $locale = new atoum\locale()))
			->and($factory->returnWhenBuild('atoum\adapter', $adapter = new atoum\adapter()))
			->and($factory->returnWhenBuild('atoum\script\arguments\parser', $argumentsParser = new atoum\script\arguments\parser()))
			->and($factory->returnWhenBuild('atoum\writers\std\out', $stdOut = new atoum\writers\std\out()))
			->and($factory->returnWhenBuild('atoum\writers\std\err', $stdErr = new atoum\writers\std\err()))
			->and($factory->returnWhenBuild('atoum\superglobals', $superglobals = new atoum\superglobals()))
			->and($factory->returnWhenBuild('atoum\scripts\builder\vcs\svn', $vcs = new atoum\scripts\builder\vcs\svn()))
			->and($builder = new scripts\builder($name = uniqid(), $factory))
			->then
				->string($builder->getName())->isEqualTo($name)
				->object($builder->getLocale())->isIdenticalTo($locale)
				->object($builder->getAdapter())->isIdenticalTo($adapter)
				->object($builder->getArgumentsParser())->isIdenticalTo($argumentsParser)
				->object($builder->getOutputWriter())->isIdenticalTo($stdOut)
				->object($builder->getErrorWriter())->isIdenticalTo($stdErr)
				->object($builder->getSuperglobals())->isIdenticalTo($superglobals)
				->array($builder->getRunnerConfigurationFiles())->isEmpty()
				->variable($builder->getVersion())->isNull()
				->variable($builder->getWorkingDirectory())->isNull()
				->variable($builder->getDestinationDirectory())->isNull()
				->variable($builder->getErrorsDirectory())->isNull()
				->variable($builder->getScoreDirectory())->isNull()
				->variable($builder->getRevisionFile())->isNull()
				->string($builder->getUnitTestRunnerScript())->isEqualTo(scripts\builder::defaultUnitTestRunnerScript)
				->variable($builder->getReportTitle())->isNull()
				->string($builder->getPharGeneratorScript())->isEqualTo(scripts\builder::defaultPharGeneratorScript)
				->object($builder->getVcs())->isIdenticalTo($vcs)
				->variable($builder->getTaggerEngine())->isNull()
		;
	}

	public function testSetVersion()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setVersion($tag = uniqid()))->isIdenticalTo($builder)
			->string($builder->getVersion())->isIdenticalTo($tag)
			->object($builder->setVersion($tag = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getVersion())->isIdenticalTo((string) $tag)
		;
	}

	public function testSetSuperglobals()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setSuperglobals($superglobals = new atoum\superglobals()))->isIdenticalTo($builder)
			->object($builder->getSuperglobals())->isIdenticalTo($superglobals);
		;
	}

	public function testGetPhp()
	{
		$superglobals = new atoum\superglobals();
		$superglobals->_SERVER['_'] = $php = uniqid();

		$builder = new scripts\builder(uniqid());
		$builder->setSuperglobals($superglobals);

		$this->assert
			->string($builder->getPhpPath())->isEqualTo($php)
		;

		unset($superglobals->_SERVER['_']);

		$builder = new scripts\builder(uniqid());
		$builder->setSuperglobals($superglobals);

		$this->assert
			->exception(function() use ($builder) {
					$builder->getPhpPath();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
		;

		$builder->setPhpPath($php = uniqid());

		$this->assert
			->string($builder->getPhpPath())->isEqualTo($php)
		;
	}

	public function testSetPhp()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setPhpPath($php = uniqid()))->isIdenticalTo($builder)
			->string($builder->getPhpPath())->isIdenticalTo($php)
		;

		$this->assert
			->object($builder->setPhpPath($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getPhpPath())->isIdenticalTo((string) $php)
		;
	}

	public function testSetReportTitle()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setReportTitle($reportTitle = uniqid()))->isIdenticalTo($builder)
			->string($builder->getReportTitle())->isEqualTo($reportTitle)
			->object($builder->setReportTitle($reportTitle = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getReportTitle())->isEqualTo((string) $reportTitle)
		;
	}

	public function testSetVcs()
	{
		$builder = new scripts\builder(uniqid());

		$vcsController = new mock\controller();
		$vcsController->__construct = function() {};

		$this->assert
			->object($builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController)))->isIdenticalTo($builder)
			->object($builder->getVcs())->isIdenticalTo($vcs)
		;
	}

	public function testSetTaggerEngine()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setTaggerEngine($taggerEngine = new atoum\scripts\tagger\engine()))->isIdenticalTo($builder)
			->object($builder->getTaggerEngine())->isIdenticalTo($taggerEngine)
		;
	}

	public function testSetUnitTestRunnerScript()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setUnitTestRunnerScript($php = uniqid()))->isIdenticalTo($builder)
			->string($builder->getUnitTestRunnerScript())->isIdenticalTo($php)
		;

		$this->assert
			->object($builder->setUnitTestRunnerScript($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getUnitTestRunnerScript())->isIdenticalTo((string) $php)
		;
	}

	public function testSetPharGeneratorScript()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setPharGeneratorScript($php = uniqid()))->isIdenticalTo($builder)
			->string($builder->getPharGeneratorScript())->isIdenticalTo($php)
		;

		$this->assert
			->object($builder->setPharGeneratorScript($php = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getPharGeneratorScript())->isIdenticalTo((string) $php)
		;
	}

	public function testSetScoreDirectory()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
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
		$builder = new scripts\builder(uniqid());

		$this->assert
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
		$builder = new scripts\builder(uniqid());

		$this->assert
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
		$builder = new scripts\builder(uniqid());

		$this->assert
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
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setRevisionFile($file = uniqid()))->isIdenticalTo($builder)
			->string($builder->getRevisionFile())->isEqualTo($file)
		;
	}

	public function testAddRunnerConfigurationFile()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->addRunnerConfigurationFile($file = uniqid()))->isIdenticalTo($builder)
			->array($builder->getRunnerConfigurationFiles())->isEqualTo(array($file))
		;
	}

	public function testSetRunFile()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setRunFile($runFile = uniqid()))->isIdenticalTo($builder)
			->string($builder->getRunFile())->isEqualTo($runFile)
		;
	}

	public function testDisableUnitTestChecking()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->boolean($builder->unitTestCheckingIsEnabled())->isTrue()
			->object($builder->disableUnitTestChecking())->isIdenticalTo($builder)
			->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
		;
	}

	public function testEnableUnitTestChecking()
	{
		$builder = new scripts\builder(uniqid());

		$builder->disableUnitTestChecking();

		$this->assert
			->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
			->object($builder->enableUnitTestChecking())->isIdenticalTo($builder)
			->boolean($builder->unitTestCheckingIsEnabled())->isTrue()
		;
	}

	public function testCheckUnitTests()
	{
		$factory = new atoum\factory();
		$factory
			->import('mageekguy\atoum')
			->returnWhenBuild('atoum\adapter', $adapter = new atoum\test\adapter())
		;

		$builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $factory);

		$builder->disableUnitTestChecking();

		$this->assert
			->boolean($builder->unitTestCheckingIsEnabled())->isFalse()
			->boolean($builder->checkUnitTests())->isTrue()
		;

		$builder->enableUnitTestChecking();

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to check unit tests, working directory is undefined')
		;

		$builder->setWorkingDirectory($workingDirectory = uniqid());

		$vcsController = new mock\controller();
		$vcsController->__construct = function() {};
		$vcsController->exportRepository = function() {};

		$builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController));

		$builder
			->setUnitTestRunnerScript($unitTestRunnerScript = uniqid())
			->setPhpPath($php = uniqid())
			->setReportTitle($reportTitle = uniqid())
			->addRunnerConfigurationFile($runnerConfigurationFile = uniqid())
		;

		$score = new \mock\mageekguy\atoum\score();

		$scoreController = $score->getMockController();
		$scoreController->getFailNumber = 0;
		$scoreController->getExceptionNumber = 0;
		$scoreController->getErrorNumber = 0;

		$adapter->sys_get_temp_dir = $tempDirectory = uniqid();
		$adapter->tempnam = $scoreFile = uniqid();
		$adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdOut, & $stdErr, & $pipes, & $resource) { $pipes = array(1 => $stdOut = uniqid(), 2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); };
		$adapter->proc_get_status = array('exit_code' => 0, 'running' => true);
		$adapter->stream_get_contents = function() { return ''; };
		$adapter->fclose = function() {};
		$adapter->proc_close = function() {};
		$adapter->file_get_contents = $scoreFileContents = uniqid();
		$adapter->unserialize = $score;
		$adapter->unlink = true;

		$command = escapeshellarg($php) . ' ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $unitTestRunnerScript) . ' -drt ' . escapeshellarg($reportTitle) . ' -ncc -sf ' . escapeshellarg($scoreFile) . ' -d ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR . 'units' . \DIRECTORY_SEPARATOR . 'classes') . ' -p ' . escapeshellarg($php) . ' -c ' . escapeshellarg($runnerConfigurationFile);

		$builderController = $builder->getMockController();
		$builderController->writeErrorInErrorsDirectory = function() {};

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($vcs)
				->call('setWorkingDirectory')
					->withArguments($workingDirectory)
					->once()
			->mock($vcs)
				->call('exportRepository')->once()
			->adapter($adapter)
				->call('sys_get_temp_dir')->once()
				->call('tempnam')->withArguments($tempDirectory, '')->once()
				->call('proc_open')->withArguments($command, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes)->once()
				->call('proc_get_status')->withArguments($resource)->once()
				->call('stream_get_contents')->withArguments($stdOut)->once()
				->call('fclose')->withArguments($stdOut)->once()
				->call('stream_get_contents')->withArguments($stdErr)->once()
				->call('fclose')->withArguments($stdErr)->once()
				->call('proc_close')->withArguments($resource)->once()
				->call('file_get_contents')->withArguments($scoreFile)->once()
				->call('unserialize')->withArguments($scoreFileContents)->once()
				->call('unlink')->withArguments($scoreFile)->once()
			->mock($score)
				->call('getFailNumber')->once()
				->call('getExceptionNumber')->once()
				->call('getErrorNumber')->once()
		;

		$adapter->proc_open = false;

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')->withArguments('Unable to execute \'' . $command . '\'')
				->once()
		;

		$adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdOut, & $stdErr, & $pipes, & $resource) { $pipes = array(1 => $stdOut = uniqid(), 2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); };

		$adapter->proc_get_status = array('exitcode' => 126, 'running' => false);

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments('Unable to find \'' . $php . '\' or it is not executable')
					->once()
		;

		$adapter->proc_get_status = array('exitcode' => 127, 'running' => false);

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments('Unable to find \'' . $php . '\' or it is not executable')
					->once()
		;

		$adapter->proc_get_status = array('exitcode' => $exitCode = rand(1, 125), 'running' => false);

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments('Command \'' . $command . '\' failed with exit code \'' . $exitCode . '\'')
					->once()
		;

		$adapter->proc_get_status = array('exitcode' => $exitCode = rand(128, PHP_INT_MAX), 'running' => false);

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments('Command \'' . $command . '\' failed with exit code \'' . $exitCode . '\'')
					->once()
		;

		$adapter->proc_get_status = array('exit_code' => 0, 'running' => true);

		$adapter->stream_get_contents = function($stream) use (& $stdOut, & $stdOutContents) { return $stream != $stdOut ? '' : $stdOutContents = uniqid(); };

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments($stdOutContents)
					->never()
		;

		$adapter->stream_get_contents = function($stream) use (& $stdErr, & $stdErrContents) { return $stream != $stdErr ? '' : $stdErrContents = uniqid(); };

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments($stdErrContents)
					->once()
		;

		$adapter->stream_get_contents = '';
		$adapter->file_get_contents = false;

		$builder->getMockController()->resetCalls();

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments('Unable to read score from file \'' . $scoreFile . '\'')
					->once()
		;

		$adapter->file_get_contents = $scoreFileContents;
		$adapter->unserialize = false;

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments('Unable to unserialize score from file \'' . $scoreFile . '\'')
					->once()
		;

		$adapter->unserialize = uniqid();

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments('Contents of file \'' . $scoreFile . '\' is not a score')
					->once()
		;

		$adapter->unserialize = $score;

		$adapter->unlink = false;

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to delete score file \'' . $scoreFile . '\'')
		;

		$adapter->unlink = true;

		$scoreController->getFailNumber = rand(1, PHP_INT_MAX);

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
		;

		$scoreController->getFailNumber = 0;
		$scoreController->getExceptionNumber = rand(1, PHP_INT_MAX);

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
		;

		$scoreController->getExceptionNumber = 0;
		$scoreController->getErrorNumber = rand(1, PHP_INT_MAX);

		$this->assert
			->boolean($builder->checkUnitTests())->isFalse()
		;
	}

	public function testDisablePharCreation()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->boolean($builder->pharCreationIsEnabled())->isTrue()
			->object($builder->disablePharCreation())->isIdenticalTo($builder)
			->boolean($builder->pharCreationIsEnabled())->isFalse()
		;
	}

	public function testEnablePharCreation()
	{
		$builder = new scripts\builder(uniqid());

		$builder->disablePharCreation();

		$this->assert
			->boolean($builder->pharCreationIsEnabled())->isFalse()
			->object($builder->enablePharCreation())->isIdenticalTo($builder)
			->boolean($builder->pharCreationIsEnabled())->isTrue()
		;
	}

	public function testCreatePhar()
	{
		$factory = new atoum\factory();
		$factory
			->import('mageekguy\atoum')
			->returnWhenBuild('atoum\adapter', $adapter = new atoum\test\adapter())
		;

		$builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $factory);

		$builder
			->setTaggerEngine($taggerEngine = new \mock\mageekguy\atoum\scripts\tagger\engine())
			->disablePharCreation()
		;

		$taggerEngine->getMockController()->tagVersion = function() {};

		$this->assert
			->boolean($builder->createPhar())->isTrue()
		;

		$builder->enablePharCreation();

		$vcsController = new mock\controller();
		$vcsController->__construct = function() {};
		$vcsController->getNextRevisions = array();
		$vcsController->exportRepository = function() {};

		$builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController));

		$this->assert
			->exception(function() use ($builder) {
						$builder->createPhar();
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to create phar, destination directory is undefined')
		;

		$builder->setDestinationDirectory($destinationDirectory = uniqid());

		$this->assert
			->exception(function() use ($builder) {
						$builder->createPhar();
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to create phar, working directory is undefined')
		;

		$builder->setWorkingDirectory($workingDirectory = uniqid());

		$builder
			->setPhpPath($php = uniqid())
			->setPharGeneratorScript($pharGeneratorScript = uniqid())
		;

		$builderController = $builder->getMockController();
		$builderController->writeErrorInErrorsDirectory = function() {};

		$adapter->file_get_contents = false;

		$this->assert
			->boolean($builder->createPhar())->isTrue()
		;

		$vcsController->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$builder->disableUnitTestChecking();

		$adapter->proc_open = false;

		$this->assert
			->boolean($builder->createPhar())->isFalse()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments('Unable to execute \'' . escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory) . '\'')
					->once()
			->mock($vcs)
				->call('setRevision')->withArguments($revision)->once()
				->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
				->call('exportRepository')->once()
		;

		$adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdErr, & $pipes, & $resource) { $pipes = array(2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); };
		$adapter->stream_get_contents = function() { return ''; };
		$adapter->fclose = function() {};
		$adapter->proc_close = function() {};
		$adapter->date = $date = uniqid();

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->mock($taggerEngine)
				->call('setVersion')
					->withArguments('nightly-' . $revision . '-' . $date)
					->once()
				->call('tagVersion')->atLeastOnce()
			->adapter($adapter)
				->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
				->call('stream_get_contents')->withArguments($stdErr)->once()
				->call('fclose')->withArguments($stdErr)->once()
				->call('proc_close')->withArguments($resource)->once()
				->call('date')->withArguments('YmdHi')->atLeastOnce()
			->mock($vcs)
				->call('setRevision')->withArguments($revision)->once()
				->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
				->call('exportRepository')->once()
		;

		$adapter->resetCalls();

		$builder->getMockController()->resetCalls();

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$this->assert
			->boolean($builder->createPhar($tag = uniqid()))->isTrue()
			->mock($taggerEngine)
				->call('setVersion')->withArguments($tag)->once()
				->call('tagVersion')->once()
			->adapter($adapter)
				->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
				->call('stream_get_contents')->withArguments($stdErr)->once()
				->call('fclose')->withArguments($stdErr)->once()
				->call('proc_close')->withArguments($resource)->once()
				->call('date')->never()
			->mock($vcs)
				->call('setRevision')->withArguments($revision)->once()
				->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
				->call('exportRepository')->once()
		;

		$adapter->resetCalls();

		$builder->getMockController()->resetCalls();

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$adapter->stream_get_contents = function() use (& $stdErrContents) { return $stdErrContents = uniqid(); };

		$this->assert
			->boolean($builder->createPhar())->isFalse()
			->adapter($adapter)
				->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
				->call('stream_get_contents')->withArguments($stdErr)->once()
				->call('fclose')->withArguments($stdErr)->once()
				->call('proc_close')->withArguments($resource)->once()
			->mock($builder)
				->call('writeErrorInErrorsDirectory')
					->withArguments($stdErrContents)
					->once()
			->mock($vcs)
				->call('setRevision')->withArguments($revision)->once()
				->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
				->call('exportRepository')->once()
		;

		$builder->setRevisionFile($revisionFile = uniqid());

		$adapter->stream_get_contents = function() { return ''; };
		$adapter->file_get_contents = false;
		$adapter->file_put_contents = function() {};

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->adapter($adapter)
				->call('file_get_contents')->withArguments($revisionFile)->once()
				->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
				->call('stream_get_contents')->withArguments($stdErr)->once()
				->call('fclose')->withArguments($stdErr)->once()
				->call('proc_close')->withArguments($resource)->once()
			->mock($vcs)
				->call('setRevision')->withArguments($revision)->once()
				->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
				->call('exportRepository')->once()
		;

		$adapter->file_get_contents = false;
		$adapter->file_put_contents = function() {};

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->adapter($adapter)
				->call('file_get_contents')->withArguments($revisionFile)->once()
				->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
				->call('stream_get_contents')->withArguments($stdErr)->once()
				->call('fclose')->withArguments($stdErr)->once()
				->call('proc_close')->withArguments($resource)->once()
				->call('file_put_contents')->withArguments($revisionFile, $revision, \LOCK_EX)->once()
			->mock($vcs)
				->call('setRevision')->withArguments($revision)->once()
				->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
				->call('exportRepository')->once()
		;

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$adapter->file_put_contents = false;

		$this->assert
			->exception(function() use ($builder) {
						$builder->createPhar();
					}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to save last revision in file \'' . $revisionFile . '\'')
		;

		$vcsController->resetCalls();
		$vcsController->getNextRevisions[1] = array(1, 2, 3);
		$vcsController->getNextRevisions[2] = array(2, 3);
		$vcsController->getNextRevisions[3] = array(3);
		$vcsController->getNextRevisions[4] = array();

		$adapter->file_put_contents = function() {};

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->adapter($adapter)
				->call('file_get_contents')->withArguments($revisionFile)->once()
				->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->exactly(3)
				->call('stream_get_contents')->withArguments($stdErr)->once()
				->call('fclose')->withArguments($stdErr)->once()
				->call('proc_close')->withArguments($resource)->once()
				->call('file_put_contents')->withArguments($revisionFile, 3, \LOCK_EX)->once()
			->mock($vcs)
				->call('setRevision')->withArguments(1)->once()
				->call('setRevision')->withArguments(2)->once()
				->call('setRevision')->withArguments(3)->once()
				->call('setWorkingDirectory')->withArguments($workingDirectory)->atLeastOnce()
				->call('exportRepository')->atLeastOnce()
		;

		$vcsController->resetCalls();
		$vcsController->getNextRevisions[1] = array(4);
		$vcsController->getNextRevisions[2] = array();

		$adapter->file_get_contents = 1;

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->adapter($adapter)
				->call('file_get_contents')->withArguments($revisionFile)->once()
				->call('proc_open')->withArguments(escapeshellarg($php) . ' -d phar.readonly=0 -f ' . escapeshellarg($workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript) . ' -- -d ' . escapeshellarg($destinationDirectory), array(2 => array('pipe', 'w')), $pipes)->once()
				->call('stream_get_contents')->withArguments($stdErr)->once()
				->call('fclose')->withArguments($stdErr)->once()
				->call('proc_close')->withArguments($resource)->once()
				->call('file_put_contents')->withArguments($revisionFile, 4, \LOCK_EX)->once()
			->mock($vcs)
				->call('setRevision')->withArguments(4)->once()
				->call('setWorkingDirectory')->withArguments($workingDirectory)->once()
				->call('exportRepository')->once()
		;
	}

	public function testRun()
	{
		$adapter = new atoum\test\adapter();
		$adapter->file_get_contents = false;
		$adapter->fopen = $runFileResource = uniqid();
		$adapter->flock = true;
		$adapter->getmypid = $pid = uniqid();
		$adapter->fwrite = function() {};
		$adapter->fclose = function() {};
		$adapter->unlink = function() {};

		$factory = new atoum\factory();
		$factory
			->import('mageekguy\atoum')
			->returnWhenBuild('atoum\adapter', $adapter)
		;

		$builder = new \mock\mageekguy\atoum\scripts\builder(uniqid(), $factory);

		$builderController = $builder->getMockController();
		$builderController->createPhar = function() {};

		$builder->setRunFile($runFile = uniqid());

		$this->assert
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

	public function testWriteInErrorDirectory()
	{
		$this->assert
			->if($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory->returnWhenBuild('atoum\adapter', $adapter = new atoum\test\adapter()))
			->and($adapter->file_put_contents = function() {})
			->and($builder = new scripts\builder(uniqid(), $factory))
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
		;

		$vcsController = new mock\controller();
		$vcsController->__construct = function() {};

		$builder->setVcs($vcs = new \mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController));

		$vcs->setRevision($revision = rand(1, PHP_INT_MAX));

		$this->assert
			->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
			->object($builder->writeErrorInErrorsDirectory($message = uniqid()))->isIdenticalTo($builder)
			->adapter($adapter)->call('file_put_contents')->withArguments($errorDirectory . \DIRECTORY_SEPARATOR . $revision, $message, \LOCK_EX | \FILE_APPEND)->once()
		;

		$adapter
			->resetCalls()
			->file_put_contents = false
		;

		$this->assert
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
