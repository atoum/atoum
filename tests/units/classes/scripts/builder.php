<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\scripts,
	\mageekguy\atoum\scripts\builder\vcs
;

require_once(__DIR__ . '/../../runner.php');

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
			->testedClass->isSubclassOf('\mageekguy\atoum\script')
		;
	}

	public function test__construct()
	{
		$adapter = new atoum\test\adapter();
		$adapter->sys_get_temp_dir = $tmpDirectory = uniqid();

		$builder = new scripts\builder($name = uniqid(), $locale = new atoum\locale(), $adapter);

		$this->assert
			->string($builder->getName())->isEqualTo($name)
			->object($builder->getLocale())->isEqualTo($locale)
			->object($builder->getAdapter())->isEqualTo($adapter)
			->object($builder->getArgumentsParser())->isInstanceOf('\mageekguy\atoum\script\arguments\parser')
			->object($builder->getOutputWriter())->isInstanceOf('\mageekguy\atoum\writers\std\out')
			->object($builder->getErrorWriter())->isInstanceOf('\mageekguy\atoum\writers\std\err')
			->object($builder->getSuperglobals())->isInstanceOf('\mageekguy\atoum\superglobals')
			->array($builder->getRunnerConfigurationFiles())->isEmpty()
			->string($builder->getRunFile())->isEqualTo($tmpDirectory . \DIRECTORY_SEPARATOR . md5(get_class($builder)))
			->string($builder->getTagRegex())->isEqualTo('/\$Rev: \d+ \$/')
			->variable($builder->getTag())->isNull()
			->variable($builder->getWorkingDirectory())->isNull()
			->variable($builder->getDestinationDirectory())->isNull()
			->variable($builder->getErrorsDirectory())->isNull()
			->variable($builder->getScoreDirectory())->isNull()
			->variable($builder->getRevisionFile())->isNull()
			->variable($builder->getUnitTestRunnerScript())->isNull()
			->variable($builder->getPharGeneratorScript())->isNull()
			->variable($builder->getReportTitle())->isNull()
			->variable($builder->getVcs())->isNull()
		;
	}

	public function testSetTag()
	{
		$builder = new scripts\builder(uniqid());

		$this->assert
			->object($builder->setTag($tag = uniqid()))->isIdenticalTo($builder)
			->string($builder->getTag())->isIdenticalTo($tag)
			->object($builder->setTag($tag = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getTag())->isIdenticalTo((string) $tag)
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
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
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

		$this->mock('\mageekguy\atoum\scripts\builder\vcs');

		$vcsController = new mock\controller();
		$vcsController->__construct = function() {};

		$this->assert
			->object($builder->setVcs($vcs = new mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController)))->isIdenticalTo($builder)
			->object($builder->getVcs())->isIdenticalTo($vcs)
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

	public function testSetFileIteratorInjector()
	{
		$builder = new scripts\builder(uniqid());

		$directory = uniqid();

		$mockController = new mock\controller();
		$mockController
			->injectInNextMockInstance()
			->__construct = function() {}
		;

		$this->mock('\recursiveDirectoryIterator');

		$iterator = new mock\recursiveDirectoryIterator($directory);

		$this->assert
			->exception(function() use ($builder, $directory) {
					$builder->getFileIterator($directory);
				}
			)
				->isInstanceOf('\unexpectedValueException')
				->hasMessage('RecursiveDirectoryIterator::__construct(' . $directory . '): failed to open dir: No such file or directory')
			->exception(function() use ($builder) {
					$builder->setFileIteratorInjector(function() {});
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('File iterator injector must take one argument')
			->object($builder->setFileIteratorInjector(function($directory) use ($iterator) { return $iterator; }))->isIdenticalTo($builder)
			->object($builder->getFileIterator(uniqid()))->isIdenticalTo($iterator)
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

	public function testTagFiles()
	{
		$adapter = new atoum\test\adapter();

		$builder = new scripts\builder(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($builder) {
						$builder->tagFiles();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to tag files, working directory is undefined')
		;

		$builder->setWorkingDirectory($workingDirectory = uniqid());

		$this->assert
			->exception(function() use ($builder) {
					$builder->tagFiles();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to tag files, tag is undefined')
		;

		$builder->setTag($tag = uniqid());

		$builder->setFileIteratorInjector(function($directory) { return null; });

		$this->assert
			->exception(function() use ($builder) {
					$builder->tagFiles();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('File iterator injector must return a \iterator instance')
		;

		$this->mock('\recursiveDirectoryIterator');

		$builder->setFileIteratorInjector(function($directory) use (& $fileIterator, & $file) {
				$fileIteratorController = new mock\controller();
				$fileIteratorController->injectInNextMockInstance();
				$fileIteratorController->__construct = function() {};
				$fileIteratorController->valid[1] = true;
				$fileIteratorController->valid[2] = false;
				$fileIteratorController->current = function() use (& $file) { return ($file = uniqid()); };
				$fileIteratorController->injectInNextMockInstance();
				return ($fileIterator = new mock\recursiveDirectoryIterator($directory));
			}
		);

		$adapter->file_get_contents = $fileContents = uniqid() . '$rev: ' . rand(1, PHP_INT_MAX) . ' $' . uniqid();
		$adapter->file_put_contents = function() {};

		$this->assert
			->object($builder->tagFiles($tag = uniqid()))->isIdenticalTo($builder)
			->mock($fileIterator)
				->call('__construct', array($workingDirectory, null))
			->adapter($adapter)
				->call('file_get_contents', array($file))
				->call('file_put_contents', array($file, preg_replace($builder->getTagRegex(), $tag, $fileContents)))
		;

		$builder
			->setTag($tag = uniqid())
		;

		$this->assert
			->object($builder->tagFiles())->isIdenticalTo($builder)
			->mock($fileIterator)->call('__construct', array($workingDirectory, null))
			->adapter($adapter)
				->call('file_get_contents', array($file))
				->call('file_put_contents', array($file, preg_replace($builder->getTagRegex(), $tag, $fileContents)))
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
		$this
			->mock($this->getTestedClassName())
			->mock('\mageekguy\atoum\score')
			->mock('\mageekguy\atoum\scripts\builder\vcs')
		;

		$builder = new mock\mageekguy\atoum\scripts\builder(uniqid(), null, $adapter = new atoum\test\adapter());

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
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to check unit tests, working directory is undefined')
		;

		$builder->setWorkingDirectory($workingDirectory = uniqid());

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to check unit tests, version control system is undefined')
		;

		$vcsController = new mock\controller();
		$vcsController->__construct = function() {};
		$vcsController->exportRepository = function() {};

		$builder->setVcs($vcs = new mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController));

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to check unit tests, unit tests runner script is undefined')
		;

		$builder
			->setUnitTestRunnerScript($unitTestRunnerScript = uniqid())
			->setPhpPath($php = uniqid())
			->setReportTitle($reportTitle = uniqid())
			->addRunnerConfigurationFile($runnerConfigurationFile = uniqid())
		;

		$score = new mock\mageekguy\atoum\score();

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

		$command = $php . ' ' . $workingDirectory . \DIRECTORY_SEPARATOR . $unitTestRunnerScript . ' -drt ' . escapeshellarg($reportTitle) . ' -ncc -sf ' . $scoreFile . ' -d ' . $workingDirectory . \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR . 'units' . \DIRECTORY_SEPARATOR . 'classes -p ' . $php . ' -c ' . $runnerConfigurationFile ;

		$builderController = $builder->getMockController();
		$builderController->writeErrorInErrorsDirectory = function() {};

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($vcs)->call('exportRepository', array($workingDirectory))
			->adapter($adapter)
				->call('sys_get_temp_dir')
				->call('tempnam', array($tempDirectory, ''))
				->call('proc_open', array($command, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes))
				->call('proc_get_status', array($resource))
				->call('stream_get_contents', array($stdOut))
				->call('fclose', array($stdOut))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->call('file_get_contents', array($scoreFile))
				->call('unserialize', array($scoreFileContents))
				->call('unlink', array($scoreFile))
			->mock($score)
				->call('getFailNumber')
				->call('getExceptionNumber')
				->call('getErrorNumber')
		;

		$adapter->proc_open = false;

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to execute \'' . $command . '\'')
		;

		$adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdOut, & $stdErr, & $pipes, & $resource) { $pipes = array(1 => $stdOut = uniqid(), 2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); };

		$adapter->proc_get_status = array('exitcode' => 126, 'running' => false);

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to find \'' . $php . '\' or it is not executable')
		;

		$adapter->proc_get_status = array('exitcode' => 127, 'running' => false);

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to find \'' . $php . '\' or it is not executable')
		;

		$adapter->proc_get_status = array('exitcode' => $exitCode = rand(1, 125), 'running' => false);

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Command \'' . $command . '\' failed with exit code \'' . $exitCode . '\'')
		;

		$adapter->proc_get_status = array('exitcode' => $exitCode = rand(128, PHP_INT_MAX), 'running' => false);

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Command \'' . $command . '\' failed with exit code \'' . $exitCode . '\'')
		;

		$adapter->proc_get_status = array('exit_code' => 0, 'running' => true);

		$adapter->stream_get_contents = function($stream) use (& $stdOut, & $stdOutContents) { return $stream != $stdOut ? '' : $stdOutContents = uniqid(); };

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($builder)
				->notCall('writeErrorInErrorsDirectory', array($stdOutContents))
		;

		$adapter->stream_get_contents = function($stream) use (& $stdErr, & $stdErrContents) { return $stream != $stdErr ? '' : $stdErrContents = uniqid(); };

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($builder)
				->call('writeErrorInErrorsDirectory', array($stdErrContents))
		;

		$adapter->file_get_contents = false;

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to read score from file \'' . $scoreFile . '\'')
		;

		$adapter->file_get_contents = $scoreFileContents;

		$adapter->unserialize = false;

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to unserialize score from file \'' . $scoreFile . '\'')
		;

		$adapter->unserialize = uniqid();

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Contents of file \'' . $scoreFile . '\' is not a score')
		;

		$adapter->unserialize = $score;

		$adapter->unlink = false;

		$this->assert
			->exception(function() use ($builder) {
					$builder->checkUnitTests();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
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
		$this
			->mock('\mageekguy\atoum\scripts\builder')
			->mock('\mageekguy\atoum\scripts\builder\vcs')
		;

		$builder = new mock\mageekguy\atoum\scripts\builder(uniqid(), null, $adapter = new atoum\test\adapter());

		$builder->disablePharCreation();

		$this->assert
			->boolean($builder->createPhar())->isTrue()
		;

		$builder->enablePharCreation();

		$this->assert
			->exception(function() use ($builder) {
						$builder->createPhar();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to create phar, version control system is undefined')
		;

		$vcsController = new mock\controller();
		$vcsController->__construct = function() {};
		$vcsController->getNextRevisions = array();
		$vcsController->exportRepository = function() {};

		$builder->setVcs($vcs = new mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController));

		$this->assert
			->exception(function() use ($builder) {
						$builder->createPhar();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to create phar, destination directory is undefined')
		;

		$builder->setDestinationDirectory($destinationDirectory = uniqid());

		$this->assert
			->exception(function() use ($builder) {
						$builder->createPhar();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to create phar, working directory is undefined')
		;

		$builder->setWorkingDirectory($workingDirectory = uniqid());

		$builder
			->setPhpPath($php = uniqid())
			->setPharGeneratorScript($pharGeneratorScript = uniqid())
		;

		$builderController = $builder->getMockController();
		$builderController->tagFiles = function() {};
		$builderController->writeErrorInErrorsDirectory = function() {};

		$adapter->file_get_contents = false;

		$this->assert
			->boolean($builder->createPhar())->isTrue()
		;

		$vcsController->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$builder->disableUnitTestChecking();

		$adapter->proc_open = false;

		$this->assert
			->exception(function() use ($builder) {
					$builder->createPhar();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to execute \'' . $php . ' -d phar.readonly=0 -f ' . $workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript . ' -- -d ' . $destinationDirectory . '\'')
			->mock($vcs)
				->call('setRevision', array($revision))
				->call('exportRepository', array($workingDirectory))
		;

		$adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdErr, & $pipes, & $resource) { $pipes = array(2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); };
		$adapter->stream_get_contents = function() { return ''; };
		$adapter->fclose = function() {};
		$adapter->proc_close = function() {};
		$adapter->date = $date = uniqid();

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->mock($builder)->call('tagFiles', array('nightly-' . $revision . '-' . $date))
			->adapter($adapter)
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript . ' -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->call('date', array('YmdHi'))
			->mock($vcs)
				->call('setRevision', array($revision))
				->call('exportRepository', array($workingDirectory))
		;

		$adapter->resetCalls();

		$builder->getMockController()->resetCalls();

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$this->assert
			->boolean($builder->createPhar($tag = uniqid()))->isTrue()
			->mock($builder)->call('tagFiles', array($tag))
			->adapter($adapter)
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript . ' -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->notCall('date')
			->mock($vcs)
				->call('setRevision', array($revision))
				->call('exportRepository', array($workingDirectory))
		;

		$adapter->resetCalls();

		$builder->getMockController()->resetCalls();

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$adapter->stream_get_contents = function() use (& $stdErrContents) { return $stdErrContents = uniqid(); };

		$this->assert
			->boolean($builder->createPhar())->isFalse()
			->adapter($adapter)
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript . ' -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
			->mock($builder)->call('writeErrorInErrorsDirectory', array($stdErrContents))
			->mock($vcs)
				->call('setRevision', array($revision))
				->call('exportRepository', array($workingDirectory))
		;

		$builder->setRevisionFile($revisionFile = uniqid());

		$adapter->stream_get_contents = function() { return ''; };
		$adapter->file_get_contents = false;
		$adapter->file_put_contents = function() {};

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->adapter($adapter)
				->call('file_get_contents', array($revisionFile))
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript . ' -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
			->mock($vcs)
				->call('setRevision', array($revision))
				->call('exportRepository', array($workingDirectory))
		;

		$adapter->file_get_contents = false;
		$adapter->file_put_contents = function() {};

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->adapter($adapter)
				->call('file_get_contents', array($revisionFile))
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript . ' -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->call('file_put_contents', array($revisionFile, $revision, \LOCK_EX))
			->mock($vcs)
				->call('setRevision', array($revision))
				->call('exportRepository', array($workingDirectory))
		;

		$vcsController->resetCalls()->getNextRevisions = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$adapter->file_put_contents = false;

		$this->assert
			->exception(function() use ($builder) {
						$builder->createPhar();
					}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
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
				->call('file_get_contents', array($revisionFile))
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript . ' -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->call('file_put_contents', array($revisionFile, 3, \LOCK_EX))
			->mock($vcs)
				->call('setRevision', array(1))
				->call('setRevision', array(2))
				->call('setRevision', array(3))
				->call('exportRepository', array($workingDirectory))
		;

		$vcsController->resetCalls();
		$vcsController->getNextRevisions[1] = array(4);
		$vcsController->getNextRevisions[2] = array();

		$adapter->file_get_contents = 1;

		$this->assert
			->boolean($builder->createPhar())->isTrue()
			->adapter($adapter)
				->call('file_get_contents', array($revisionFile))
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . \DIRECTORY_SEPARATOR . $pharGeneratorScript . ' -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->call('file_put_contents', array($revisionFile, 4, \LOCK_EX))
			->mock($vcs)
				->call('setRevision', array(4))
				->call('exportRepository', array($workingDirectory))
		;
	}

	public function testRun()
	{
		$this->mock('\mageekguy\atoum\scripts\builder');

		$adapter = new atoum\test\adapter();
		$adapter->file_get_contents = false;
		$adapter->fopen = $runFileResource = uniqid();
		$adapter->flock = true;
		$adapter->getmypid = $pid = uniqid();
		$adapter->fwrite = function() {};
		$adapter->fclose = function() {};
		$adapter->unlink = function() {};

		$builder = new mock\mageekguy\atoum\scripts\builder(uniqid(), null, $adapter);

		$builderController = $builder->getMockController();
		$builderController->createPhar = function() {};

		$builder->setRunFile($runFile = uniqid());

		$builder->run();

		$this->define
			->mock($builder)->is('builder')
		;

		$this->assert
			->builder->call('createPhar')
			->adapter($adapter)
				->call('file_get_contents', array($runFile))
				->call('fopen', array($runFile, 'w+'))
				->call('flock', array($runFileResource, \LOCK_EX | \LOCK_NB))
				->call('fwrite', array($runFileResource, $pid))
				->call('fclose', array($runFileResource))
				->call('unlink', array($runFile))
		;
	}

	public function testWriteInErrorDirectory()
	{
		$adapter = new atoum\test\adapter();

		$builder = new scripts\builder(uniqid(), null, $adapter);

		$adapter->file_put_contents = function() {};

		$this->assert
			->variable($builder->getErrorsDirectory())->isNull()
			->object($builder->writeErrorInErrorsDirectory(uniqid()))->isIdenticalTo($builder)
			->adapter($adapter)->notCall('file_put_contents')
		;

		$builder->setErrorsDirectory($errorDirectory = uniqid());

		$this->assert
			->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
			->exception(function() use ($builder) {
						$builder->writeErrorInErrorsDirectory(uniqid());
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic')
					->hasMessage('Revision is undefined')
			->adapter($adapter)->notCall('file_put_contents')
		;

		$this->mock('\mageekguy\atoum\scripts\builder\vcs');

		$vcsController = new mock\controller();
		$vcsController->__construct = function() {};

		$builder->setVcs($vcs = new mock\mageekguy\atoum\scripts\builder\vcs(null, $vcsController));

		$vcs->setRevision($revision = rand(1, PHP_INT_MAX));

		$this->assert
			->string($builder->getErrorsDirectory())->isEqualTo($errorDirectory)
			->object($builder->writeErrorInErrorsDirectory($message = uniqid()))->isIdenticalTo($builder)
			->adapter($adapter)->call('file_put_contents', array($errorDirectory . \DIRECTORY_SEPARATOR . $revision, $message, \LOCK_EX | \FILE_APPEND))
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
					->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to save error in file \'' . $errorDirectory . \DIRECTORY_SEPARATOR . $revision . '\'')
			->adapter($adapter)->call('file_put_contents', array($errorDirectory . \DIRECTORY_SEPARATOR . $revision, $message, \LOCK_EX | \FILE_APPEND))
		;
	}
}

?>
