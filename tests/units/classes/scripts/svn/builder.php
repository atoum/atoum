<?php

namespace mageekguy\atoum\tests\units\scripts\svn;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;
use \mageekguy\atoum\scripts\svn;

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

		$builder = new svn\builder(uniqid(), null, $adapter = new atoum\adapter());

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

		$builder = new svn\builder(uniqid(), null, $adapter = new atoum\adapter());

		$this->assert
			->object($builder->setWorkingDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getWorkingDirectory())->isEqualTo($directory)
		;
	}

	public function testCheckout()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter = new atoum\adapter());

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

		$mockGenerator = new mock\generator();
		$mockGenerator->generate($this->getTestedClassName());

		$builder = new mock\mageekguy\atoum\scripts\svn\builder(uniqid(), null, $adapter = new atoum\adapter());

		$builder->getMockController()->checkout = function() {};

		$score = new atoum\score();

		$adapter->sys_get_temp_dir = $tempDirectory = uniqid();
		$adapter->tempnam = $tempName = uniqid();
		$adapter->proc_open = uniqid();
		$adapter->stream_get_contents = function() {};
		$adapter->fclose = function() {};
		$adapter->proc_close = function() {};
		$adapter->file_get_contents = $scoreFileContents = uniqid();
		$adapter->unserialize = $score;
		$adapter->unlink = true;

		$this->assert
			->boolean($builder->checkUnitTests())->isTrue()
			->mock($builder)->call('checkout')
		;
	}
}

?>
