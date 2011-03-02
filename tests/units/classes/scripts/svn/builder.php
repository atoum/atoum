<?php

namespace mageekguy\atoum\tests\units\scripts\svn;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\scripts\svn
;

require_once(__DIR__ . '/../../../runner.php');

class builder extends atoum\test
{
	public function beforeTestMethod($testMethod)
	{
		if (defined('SVN_REVISION_HEAD') === false)
		{
			define('SVN_REVISION_HEAD', -1);
		}
	}

	public function test__construct()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = false;

		$this->assert
			->exception(function() use ($adapter) {
						new svn\builder(uniqid(), null, $adapter);
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('PHP extension svn is not available, please install it')
		;

		$adapter->extension_loaded = true;

		$builder = new svn\builder($name = uniqid(), $locale = new atoum\locale(), $adapter);

		$this->assert
			->string($builder->getName())->isEqualTo($name)
			->object($builder->getLocale())->isEqualTo($locale)
			->object($builder->getAdapter())->isEqualTo($adapter)
			->object($builder->getArgumentsParser())->isInstanceOf('\mageekguy\atoum\script\arguments\parser')
			->object($builder->getOutputWriter())->isInstanceOf('\mageekguy\atoum\writers\std\out')
			->object($builder->getErrorWriter())->isInstanceOf('\mageekguy\atoum\writers\std\err')
			->object($builder->getSuperglobals())->isInstanceOf('\mageekguy\atoum\superglobals')
		;
	}

	public function testSetSuperglobals()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setSuperglobals($superglobals = new atoum\superglobals()))->isIdenticalTo($builder)
			->object($builder->getSuperglobals())->isIdenticalTo($superglobals);
		;
	}

	public function testSetRepositoryUrl()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setRepositoryUrl($url = uniqid()))->isIdenticalTo($builder)
			->string($builder->getRepositoryUrl())->isEqualTo($url)
			->object($builder->setRepositoryUrl($url = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getRepositoryUrl())->isEqualTo((string) $url)
		;
	}

	public function testSetScoreDirectory()
	{
		$builder = new svn\builder(uniqid());

		$this->assert
			->variable($builder->getScoreDirectory())->isNull()
			->object($builder->setScoreDirectory($scoreDirectory = uniqid()))->isIdenticalTo($builder)
			->string($builder->getScoreDirectory())->isEqualTo($scoreDirectory)
		;
	}

	public function testSetErrorsDirectory()
	{
		$builder = new svn\builder(uniqid());

		$this->assert
			->variable($builder->getErrorsDirectory())->isNull()
			->object($builder->setErrorsDirectory($errorsDirectory = uniqid()))->isIdenticalTo($builder)
			->string($builder->getErrorsDirectory())->isEqualTo($errorsDirectory)
		;
	}

	public function testSetUsername()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setUsername($username = uniqid()))->isIdenticalTo($builder)
			->string($builder->getUsername())->isEqualTo($username)
			->object($builder->setUsername($username = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getUsername())->isEqualTo((string) $username)
		;
	}

	public function testSetPassword()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setPassword($password = uniqid()))->isIdenticalTo($builder)
			->string($builder->getPassword())->isEqualTo($password)
			->object($builder->setPassword($password = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getPassword())->isEqualTo((string) $password)
		;
	}

	public function testSetRevision()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setRevision($revisionNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
			->integer($builder->getRevision())->isEqualTo($revisionNumber)
			->object($builder->setRevision($revisionNumber = uniqid()))->isIdenticalTo($builder)
			->integer($builder->getRevision())->isEqualTo((int) $revisionNumber)
		;
	}

	public function testResetRevision()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$builder->setRevision(rand(1, PHP_INT_MAX));

		$this->assert
			->variable($builder->getRevision())->isNotNull()
			->object($builder->resetRevision())->isIdenticalTo($builder)
			->variable($builder->getRevision())->isNull()
		;
	}

	public function testSetRevisionFile()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setRevisionFile($file = uniqid()))->isIdenticalTo($builder)
			->string($builder->getRevisionFile())->isEqualTo($file)
		;
	}

	public function testGetLogs()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($builder) {
						$builder->getLogs();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to get logs, repository url is undefined')
		;

		$builder->setRepositoryUrl($repositoryUrl = uniqid());

		$adapter->svn_log = array();

		$this->assert
			->array($builder->getLogs())->isEmpty()
			->adapter($adapter)->call('svn_log', array($repositoryUrl, null, SVN_REVISION_HEAD))
		;

		$builder->setRevision($revision = rand(1, PHP_INT_MAX));

		$this->assert
			->array($builder->getLogs())->isEmpty()
			->adapter($adapter)->call('svn_log', array($repositoryUrl, $revision, SVN_REVISION_HEAD))
		;
	}

	public function testSetDestinationDirectory()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setDestinationDirectory($directory = uniqid()))->isIdenticalTo($builder)
			->string($builder->getDestinationDirectory())->isEqualTo($directory)
			->object($builder->setDestinationDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getDestinationDirectory())->isEqualTo($directory)
		;
	}

	public function testSetWorkingDirectory()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setWorkingDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getWorkingDirectory())->isEqualTo($directory)
		;
	}

	public function testBuildPhar()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->boolean($builder->pharWillBeBuilt())->isTrue()
			->object($builder->buildPhar(false))->isIdenticalTo($builder)
			->boolean($builder->pharWillBeBuilt())->isFalse()
			->object($builder->buildPhar(true))->isIdenticalTo($builder)
			->boolean($builder->pharWillBeBuilt())->isTrue()
		;
	}

	public function testCheckout()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->exception(function() use ($builder) {
						$builder->checkout();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to checkout repository, its url is undefined')
		;

		$builder->setRepositoryUrl($repositoryUrl = uniqid());

		$this->assert
			->exception(function() use ($builder) {
						$builder->checkout();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to checkout repository, working directory is undefined')
		;

		$builder->setWorkingDirectory($workingDirectory = uniqid());

		$adapter->svn_auth_set_parameter = null;
		$adapter->svn_checkout = true;

		$adapter->svn_log = array();

		$this->assert
			->variable($builder->getRevision())->isNull()
			->exception(function() use ($builder) {
						$builder->checkout();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to retrieve last revision number from repository \'' . $repositoryUrl . '\'')
			->variable($builder->getRevision())->isNull()
		;

		$adapter->svn_log = array(array('rev' => $revision = 1));

		$this->assert
			->variable($builder->getRevision())->isNull()
			->object($builder->checkout())->isIdenticalTo($builder)
			->adapter($adapter)
				->notCall('svn_auth_set_parameter')
				->call('svn_checkout', array($repositoryUrl, $workingDirectory, $revision))
			->integer($builder->getRevision())->isEqualTo($revision)
		;

		$builder->setRevision($revision = rand(2, PHP_INT_MAX));

		$this->assert
			->integer($builder->getRevision())->isEqualTo($revision)
			->object($builder->checkout())->isIdenticalTo($builder)
			->adapter($adapter)
				->notCall('svn_auth_set_parameter')
				->call('svn_checkout', array($repositoryUrl, $workingDirectory, $revision))
			->integer($builder->getRevision())->isEqualTo($revision)
		;

		$builder->setUsername($username = uniqid());

		$this->assert
			->integer($builder->getRevision())->isEqualTo($revision)
			->object($builder->checkout())->isIdenticalTo($builder)
			->adapter($adapter)
				->call('svn_auth_set_parameter', array(SVN_AUTH_PARAM_DEFAULT_USERNAME, $username))
				->notCall('svn_auth_set_parameter', array(SVN_AUTH_PARAM_DEFAULT_PASSWORD))
				->call('svn_checkout', array($repositoryUrl, $workingDirectory, $revision))
			->integer($builder->getRevision())->isEqualTo($revision)
		;

		$builder->setPassword($password = uniqid());

		$this->assert
			->integer($builder->getRevision())->isEqualTo($revision)
			->object($builder->checkout())->isIdenticalTo($builder)
			->adapter($adapter)
				->call('svn_auth_set_parameter', array(SVN_AUTH_PARAM_DEFAULT_USERNAME, $username))
				->call('svn_auth_set_parameter', array(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $password))
				->call('svn_checkout', array($repositoryUrl, $workingDirectory, $revision))
			->integer($builder->getRevision())->isEqualTo($revision)
		;

		$adapter->svn_checkout = false;

		$this->assert
			->exception(function() use ($builder) {
						$builder->checkout();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to checkout repository \'' . $repositoryUrl . '\' in working directory \'' . $workingDirectory . '\'')
		;
	}

	public function testCheckUnitTests()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$superglobals = new atoum\superglobals();
		$superglobals->_SERVER['_'] = $php = uniqid();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate($this->getTestedClassName())
			->generate('\mageekguy\atoum\score')
		;

		$builder = new mock\mageekguy\atoum\scripts\svn\builder(uniqid(), null, $adapter);

		$builder
			->setSuperglobals($superglobals)
			->setWorkingDirectory($workingDirectory = uniqid())
		;

		$builderController = $builder->getMockController();
		$builderController->checkout = function() {};
		$builderController->writeErrorInErrorsDirectory = function() {};

		$score = new mock\mageekguy\atoum\score();

		$scoreController = $score->getMockController();
		$scoreController->getFailNumber = 0;
		$scoreController->getExceptionNumber = 0;
		$scoreController->getErrorNumber = 0;

		$adapter->sys_get_temp_dir = $tempDirectory = uniqid();
		$adapter->tempnam = $scoreFile = uniqid();
		$adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdOut, & $stdErr, & $pipes, & $resource) { $pipes = array(1 => $stdOut = uniqid(), 2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); };
		$adapter->stream_get_contents = function() { return ''; };
		$adapter->fclose = function() {};
		$adapter->proc_close = function() {};
		$adapter->file_get_contents = $scoreFileContents = uniqid();
		$adapter->unserialize = $score;
		$adapter->unlink = true;

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($builder)->call('checkout')
			->adapter($adapter)
				->call('sys_get_temp_dir')
				->call('tempnam', array($tempDirectory, ''))
				->call('proc_open', array($php . ' ' . $workingDirectory . '/scripts/runner.php -ncc -nr -sf ' . $scoreFile . ' -d ' . $workingDirectory . '/tests/units/classes', array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes))
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
				->hasMessage('Unable to execute \'' . $php . ' ' . $workingDirectory . '/scripts/runner.php -ncc -nr -sf ' . $scoreFile . ' -d ' . $workingDirectory . '/tests/units/classes\'')
		;

		$adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdOut, & $stdErr, & $pipes, & $resource) { $pipes = array(1 => $stdOut = uniqid(), 2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); };

		$adapter->stream_get_contents = function($stream) use (& $stdOut, & $stdOutContents) { return $stream != $stdOut ? '' : $stdOutContents = uniqid(); };

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($builder)->call('writeErrorInErrorsDirectory', array($stdOutContents))
		;

		$adapter->stream_get_contents = function($stream) use (& $stdErr, & $stdErrContents) { return $stream != $stdErr ? '' : $stdErrContents = uniqid(); };

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($builder)->call('writeErrorInErrorsDirectory', array($stdErrContents))
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

	public function testBuild()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\scripts\svn\builder');

		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$superglobals = new atoum\superglobals();
		$superglobals->_SERVER['_'] = $php = uniqid();

		$builder = new mock\mageekguy\atoum\scripts\svn\builder(uniqid(), null, $adapter);

		$this->assert
			->variable($builder->getDestinationDirectory())->isNull()
			->exception(function() use ($builder) {
						$builder->build();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to build phar, destination directory is undefined')
		;

		$builder->setSuperglobals($superglobals);
		$builder->setWorkingDirectory($workingDirectory = uniqid());
		$builder->setDestinationDirectory($destinationDirectory = uniqid());

		$builderController = $builder->getMockController();
		$builderController->getNextRevisionNumbers = array();

		$adapter->file_get_contents = false;

		$this->assert
			->boolean($builder->build())->isFalse()
		;

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };
		$builderController->checkUnitTests = false;

		$this->assert
			->variable($builder->getRevision())->isNull()
			->boolean($builder->build())->isFalse()
			->integer($builder->getRevision())->isEqualTo($revision)
		;

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };
		$builderController->checkUnitTests = true;

		$adapter->proc_open = false;

		$builder->resetRevision();

		$this->assert
			->variable($builder->getRevision())->isNull()
			->exception(function() use ($builder) {
					$builder->build();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to execute \'' . $php . ' -d phar.readonly=0 -f ' . $workingDirectory . '/scripts/phar/generator.php -- -d ' . $destinationDirectory . '\'')
			->integer($builder->getRevision())->isEqualTo($revision)
		;

		$adapter->proc_open = function($bin, $descriptors, & $stream) use (& $stdErr, & $pipes, & $resource) { $pipes = array(2 => $stdErr = uniqid()); $stream = $pipes; return ($resource = uniqid()); };
		$adapter->stream_get_contents = function() { return ''; };
		$adapter->fclose = function() {};
		$adapter->proc_close = function() {};

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };
		$builderController->checkUnitTests = true;
		$builderController->writeErrorInErrorsDirectory = function() {};

		$builder->resetRevision();

		$this->assert
			->variable($builder->getRevision())->isNull()
			->boolean($builder->build())->isTrue()
			->integer($builder->getRevision())->isEqualTo($revision)
			->adapter($adapter)
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . '/scripts/phar/generator.php -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
		;

		$adapter->stream_get_contents = function() use (& $stdErrContents) { return $stdErrContents = uniqid(); };

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$builder->resetRevision();

		$this->assert
			->variable($builder->getRevision())->isNull()
			->boolean($builder->build())->isFalse()
			->integer($builder->getRevision())->isEqualTo($revision)
			->adapter($adapter)
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . '/scripts/phar/generator.php -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
			->mock($builder)->call('writeErrorInErrorsDirectory', array($stdErrContents))
		;

		$builder->setRevisionFile($revisionFile = uniqid());

		$adapter->stream_get_contents = function() { return ''; };
		$adapter->file_get_contents = false;
		$adapter->file_put_contents = function() {};

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$builder->resetRevision();

		$this->assert
			->variable($builder->getRevision())->isNull()
			->boolean($builder->build())->isTrue()
			->integer($builder->getRevision())->isEqualTo($revision)
			->adapter($adapter)
				->call('file_get_contents', array($revisionFile))
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . '/scripts/phar/generator.php -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
		;

		$adapter->file_get_contents = false;
		$adapter->file_put_contents = function() {};

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$builder->resetRevision();

		$this->assert
			->variable($builder->getRevision())->isNull()
			->boolean($builder->build())->isTrue()
			->integer($builder->getRevision())->isEqualTo($revision)
			->adapter($adapter)
				->call('file_get_contents', array($revisionFile))
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . '/scripts/phar/generator.php -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->call('file_put_contents', array($revisionFile, $revision, \LOCK_EX))
		;

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array($revision = rand(1, PHP_INT_MAX)); };

		$builder->resetRevision();

		$adapter->file_put_contents = false;

		$this->assert
			->exception(function() use ($builder) {
						$builder->build();
					}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to save last revision in file \'' . $revisionFile . '\'')
		;

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array(1, 2, 3); };

		$builder->resetRevision();

		$adapter->file_put_contents = function() {};

		$this->assert
			->variable($builder->getRevision())->isNull()
			->boolean($builder->build())->isTrue()
			->integer($builder->getRevision())->isEqualTo(3)
			->adapter($adapter)
				->call('file_get_contents', array($revisionFile))
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . '/scripts/phar/generator.php -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->call('file_put_contents', array($revisionFile, 3, \LOCK_EX))
		;

		$builderController->getNextRevisionNumbers = function() use (& $revision) { static $i = 0; return ++$i > 1 ? array() : array(2, 3, 4); };

		$builder->resetRevision();

		$adapter->file_get_contents = 1;

		$this->assert
			->variable($builder->getRevision())->isNull()
			->boolean($builder->build())->isTrue()
			->integer($builder->getRevision())->isEqualTo(4)
			->adapter($adapter)
				->call('file_get_contents', array($revisionFile))
				->call('proc_open', array($php . ' -d phar.readonly=0 -f ' . $workingDirectory . '/scripts/phar/generator.php -- -d ' . $destinationDirectory, array(2 => array('pipe', 'w')), $pipes))
				->call('stream_get_contents', array($stdErr))
				->call('fclose', array($stdErr))
				->call('proc_close', array($resource))
				->call('file_put_contents', array($revisionFile, 4, \LOCK_EX))
		;
	}
}

?>
