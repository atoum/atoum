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

	public function testSetRepositoryUrl()
	{
		$builder = new svn\builder(uniqid());

		$this->assert
			->object($builder->setRepositoryUrl($url = uniqid()))->isIdenticalTo($builder)
			->string($builder->getRepositoryUrl())->isEqualTo($url)
			->object($builder->setRepositoryUrl($url = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getRepositoryUrl())->isEqualTo((string) $url)
		;
	}

	public function testSetUsername()
	{
		$builder = new svn\builder(uniqid());

		$this->assert
			->object($builder->setUsername($username = uniqid()))->isIdenticalTo($builder)
			->string($builder->getUsername())->isEqualTo($username)
			->object($builder->setUsername($username = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getUsername())->isEqualTo((string) $username)
		;
	}

	public function testSetPassword()
	{
		$builder = new svn\builder(uniqid());

		$this->assert
			->object($builder->setPassword($password = uniqid()))->isIdenticalTo($builder)
			->string($builder->getPassword())->isEqualTo($password)
			->object($builder->setPassword($password = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($builder)
			->string($builder->getPassword())->isEqualTo((string) $password)
		;
	}

	public function testSetLastRevision()
	{
		$builder = new svn\builder(uniqid());

		$this->assert
			->object($builder->setLastRevision($revisionNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($builder)
			->integer($builder->getLastRevision())->isEqualTo($revisionNumber)
			->object($builder->setLastRevision($revisionNumber = uniqid()))->isIdenticalTo($builder)
			->integer($builder->getLastRevision())->isEqualTo((int) $revisionNumber)
		;
	}

	public function testGetSvnLogs()
	{
		$builder = new svn\builder(uniqid(), null, $adapter = new atoum\adapter());

		$this->assert
			->exception(function() use ($builder) {
						$builder->getSvnLogs();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->hasMessage('Repository url is undefined')
		;

		$builder->setRepositoryUrl($repositoryUrl = uniqid());

		$adapter->svn_log = array();

		$this->assert
			->array($builder->getSvnLogs())->isEmpty()
			->adapter($adapter)->call('svn_log', array($repositoryUrl, null, SVN_REVISION_HEAD))
		;
	}
}

?>
