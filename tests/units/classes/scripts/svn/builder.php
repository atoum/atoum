<?php

namespace mageekguy\atoum\tests\units\scripts\svn;

use \mageekguy\atoum;
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

	public function testSetLastRevision()
	{
		$adapter = new atoum\adapter();

		$adapter->extension_loaded = true;

		$builder = new svn\builder(uniqid(), null, $adapter);

		$this->assert
			->object($builder->setLastRevision($revisionNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
			->integer($builder->getLastRevision())->isEqualTo($revisionNumber)
			->object($builder->setLastRevision($revisionNumber = uniqid()))->isIdenticalTo($builder)
			->integer($builder->getLastRevision())->isEqualTo((int) $revisionNumber)
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

		$builder->setLastRevision($lastRevision = rand(1, PHP_INT_MAX));

		$this->assert
			->array($builder->getLogs())->isEmpty()
			->adapter($adapter)->call('svn_log', array($repositoryUrl, $lastRevision, SVN_REVISION_HEAD))
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

		$this->assert
			->object($builder->checkout())->isIdenticalTo($builder)
			->adapter($adapter)
				->notCall('svn_auth_set_parameter')
				->call('svn_checkout', array($repositoryUrl, $workingDirectory, SVN_REVISION_HEAD))
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

		$builder = new svn\builder(uniqid(), null, $adapter = new atoum\adapter());
	}
}

?>
