<?php

namespace mageekguy\atoum\tests\units\scripts\builder\vcs;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\mock\stream,
	mageekguy\atoum\scripts\builder\vcs
;

require_once __DIR__ . '/../../../../runner.php';

class svn extends atoum\test
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
		$this->testedClass->isSubclassOf('mageekguy\atoum\scripts\builder\vcs');
	}

	public function test__construct()
	{
		$this
			->if($svn = new vcs\svn())
			->then
				->object($svn->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->variable($svn->getRepositoryUrl())->isNull()
			->if($svn = new vcs\svn($adapter = new atoum\adapter()))
			->then
				->object($svn->getAdapter())->isIdenticalTo($adapter)
				->variable($svn->getRepositoryUrl())->isNull()
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($svn = new vcs\svn($adapter))
			->then
				->object($svn->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($svn)
				->object($svn->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetRepositoryUrl()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($svn = new vcs\svn($adapter))
			->then
				->object($svn->setRepositoryUrl($url = uniqid()))->isIdenticalTo($svn)
				->string($svn->getRepositoryUrl())->isEqualTo($url)
				->object($svn->setRepositoryUrl($url = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($svn)
				->string($svn->getRepositoryUrl())->isEqualTo((string) $url)
		;
	}

	public function testSetRevision()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($svn = new vcs\svn($adapter))
			->then
				->object($svn->setRevision($revisionNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($svn)
				->integer($svn->getRevision())->isEqualTo($revisionNumber)
				->object($svn->setRevision($revisionNumber = uniqid()))->isIdenticalTo($svn)
				->integer($svn->getRevision())->isEqualTo((int) $revisionNumber)
		;
	}

	public function testSetUsername()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($svn = new vcs\svn($adapter))
			->then
				->object($svn->setUsername($username = uniqid()))->isIdenticalTo($svn)
				->string($svn->getUsername())->isEqualTo($username)
				->object($svn->setUsername($username = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($svn)
				->string($svn->getUsername())->isEqualTo((string) $username)
		;
	}

	public function testSetPassword()
	{
		$adapter = new atoum\test\adapter();
		$adapter->extension_loaded = true;

		$svn = new vcs\svn($adapter);

		$this->assert
			->object($svn->setPassword($password = uniqid()))->isIdenticalTo($svn)
			->string($svn->getPassword())->isEqualTo($password)
			->object($svn->setPassword($password = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($svn)
			->string($svn->getPassword())->isEqualTo((string) $password)
		;
	}

	public function testGetWorkingDirectoryIterator()
	{
		$adapter = new atoum\test\adapter();
		$adapter->extension_loaded = true;

		$svn = new vcs\svn($adapter);

		$svn->setWorkingDirectory(__DIR__);

		$this->assert
			->object($recursiveIteratorIterator = $svn->getWorkingDirectoryIterator())->isInstanceOf('recursiveIteratorIterator')
			->object($recursiveDirectoryIterator = $recursiveIteratorIterator->getInnerIterator())->isInstanceOf('recursiveDirectoryIterator')
			->string($recursiveDirectoryIterator->current()->getPathInfo()->getPathname())->isEqualTo(__DIR__)
		;
	}

	public function testGetNextRevisions()
	{
		$adapter = new atoum\test\adapter();
		$adapter->extension_loaded = true;

		$svn = new vcs\svn($adapter);

		$adapter->svn_auth_set_parameter = function() {};

		$this->assert
			->exception(function() use ($svn) {
						$svn->getNextRevisions();
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to get logs, repository url is undefined')
			->adapter($adapter)
				->call('svn_auth_set_parameter')->withArguments(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)->never()
				->call('svn_log')->never()
		;

		$svn->setRepositoryUrl($repositoryUrl = uniqid());

		$adapter->svn_auth_set_parameter = function() {};
		$adapter->svn_log = array();
		$adapter->resetCalls();

		$this->assert
			->array($svn->getNextRevisions())->isEmpty()
			->adapter($adapter)
				->call('svn_auth_set_parameter')->withArguments(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)->once()
				->call('svn_log')->withArguments($repositoryUrl, 1, SVN_REVISION_HEAD)->once()
		;

		$svn->setRevision($revision = rand(1, PHP_INT_MAX));

		$adapter->resetCalls();

		$this->assert
			->array($svn->getNextRevisions())->isEmpty()
			->adapter($adapter)
				->call('svn_auth_set_parameter')->withArguments(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)->once()
				->call('svn_log')->withArguments($repositoryUrl, $revision, SVN_REVISION_HEAD)->once()
		;

		$adapter->resetCalls();

		$adapter->svn_log = array(uniqid() => uniqid());

		$this->assert
			->array($svn->getNextRevisions())->isEmpty()
			->adapter($adapter)->call('svn_log')->withArguments($repositoryUrl, $revision, SVN_REVISION_HEAD)->once()
		;

		$adapter->resetCalls();

		$adapter->svn_log = array(
			array('rev' => $revision1 = uniqid()),
			array('rev' => $revision2 = uniqid()),
			array('rev' => $revision3 = uniqid())
		);

		$this->assert
			->array($svn->getNextRevisions())->isEqualTo(array($revision1, $revision2, $revision3))
			->adapter($adapter)->call('svn_log')->withArguments($repositoryUrl, $revision, SVN_REVISION_HEAD)->once()
		;
	}

	public function testSetExportDirectory()
	{
		$adapter = new atoum\test\adapter();
		$adapter->extension_loaded = true;

		$svn = new \mock\mageekguy\atoum\scripts\builder\vcs\svn($adapter);

		$this->assert
			->object($svn->setWorkingDirectory($workingDirectory = uniqid()))->isIdenticalTo($svn)
			->string($svn->getWorkingDirectory())->isEqualTo($workingDirectory)
			->object($svn->setWorkingDirectory($workingDirectory = rand(1, PHP_INT_MAX)))->isIdenticalTo($svn)
			->string($svn->getWorkingDirectory())->isIdenticalTo((string) $workingDirectory)
		;
	}

	public function testExportRepository()
	{
		$adapter = new atoum\test\adapter();
		$adapter->extension_loaded = true;

		$svn = new \mock\mageekguy\atoum\scripts\builder\vcs\svn($adapter);

		$svn->getMockController()->cleanWorkingDirectory = $svn;

		$this->assert
			->exception(function() use ($svn) {
						$svn->exportRepository(uniqid());
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to export repository, repository url is undefined')
			->adapter($adapter)
				->call('svn_auth_set_parameter')->withArguments(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)->never()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_USERNAME, $svn->getUsername())->never()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $svn->getPassword())->never()
				->call('svn_checkout')->never()
		;

		$svn
			->setRepositoryUrl($repositoryUrl = uniqid())
			->setWorkingDirectory($workingDirectory = __DIR__)
		;

		$adapter->resetCalls();
		$adapter->svn_checkout = false;
		$adapter->svn_auth_set_parameter = function() {};

		$this->assert
			->exception(function() use ($svn) {
						$svn->exportRepository();
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to checkout repository \'' . $repositoryUrl . '\' in directory \'' . $workingDirectory . '\'')
			->adapter($adapter)
				->call('svn_auth_set_parameter')->withArguments(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)->once()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_USERNAME, $svn->getUsername())->never()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $svn->getPassword())->never()
				->call('svn_checkout')->withArguments($svn->getRepositoryUrl(), $workingDirectory, $svn->getRevision())->once()
			->mock($svn)
				->call('cleanWorkingDirectory')->once()
		;

		$svn
			->setUsername(uniqid())
			->getMockController()->resetCalls()
		;

		$adapter->resetCalls();

		$this->assert
			->exception(function() use ($svn) {
						$svn->exportRepository();
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to checkout repository \'' . $repositoryUrl . '\' in directory \'' . $workingDirectory . '\'')
			->adapter($adapter)
				->call('svn_auth_set_parameter')->withArguments(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)->once()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_USERNAME, $svn->getUsername())->once()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $svn->getPassword())->never()
				->call('svn_checkout')->withArguments($svn->getRepositoryUrl(), $workingDirectory, $svn->getRevision())->once()
			->mock($svn)
				->call('cleanWorkingDirectory')->once()
		;

		$svn
			->setPassword(uniqid())
			->getMockController()->resetCalls()
		;

		$adapter->resetCalls();

		$this->assert
			->exception(function() use ($svn) {
						$svn->exportRepository();
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to checkout repository \'' . $repositoryUrl . '\' in directory \'' . $workingDirectory . '\'')
			->adapter($adapter)
				->call('svn_auth_set_parameter')->withArguments(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)->once()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_USERNAME, $svn->getUsername())->once()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $svn->getPassword())->once()
				->call('svn_checkout')->withArguments($svn->getRepositoryUrl(), $workingDirectory, $svn->getRevision())->once()
			->mock($svn)
				->call('cleanWorkingDirectory')->once()
		;

		$svn->getMockController()->resetCalls();

		$adapter->svn_checkout = true;
		$adapter->resetCalls();

		$this->assert
			->object($svn->exportRepository())->isIdenticalTo($svn)
			->adapter($adapter)
				->call('svn_auth_set_parameter')->withArguments(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true)->once()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_USERNAME, $svn->getUsername())->once()
				->call('svn_auth_set_parameter')->withArguments(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $svn->getPassword())->once()
				->call('svn_checkout')->withArguments($svn->getRepositoryUrl(), $workingDirectory, $svn->getRevision())->once()
			->mock($svn)
				->call('cleanWorkingDirectory')->once()
		;
	}

	public function testCleanWorkingDirectory()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($adapter->unlink = function() {})
			->and($adapter->rmdir = function() {})
			->and($workingDirectory = stream::get('workingDirectory'))
			->and($workingDirectory->opendir = true)
			->and($workingDirectory->readdir[1] = $aDirectory = stream::getSubStream($workingDirectory))
			->and($aDirectory->opendir = true)
			->and($aDirectory->readdir[1] = $firstFile = stream::getSubStream($aDirectory))
			->and($firstFile->unlink = true)
			->and($aDirectory->readdir[2] = $secondFile = stream::getSubStream($aDirectory))
			->and($secondFile->unlink = true)
			->and($aDirectory->readdir[3] = false)
			->and($workingDirectory->readdir[2] = $emptyDirectory = stream::getSubStream($workingDirectory))
			->and($emptyDirectory->opendir = true)
			->and($emptyDirectory->readdir[1] = false)
			->and($workingDirectory->readdir[3] = $anOtherDirectory = stream::getSubStream($workingDirectory))
			->and($anOtherDirectory->opendir = true)
			->and($anOtherDirectory->readdir[1] = $anOtherFirstFile =  stream::getSubStream($anOtherDirectory))
			->and($anOtherFirstFile->unlink = true)
			->and($anOtherDirectory->readdir[2] = $anOtherSecondFile = stream::getSubStream($anOtherDirectory))
			->and($anOtherSecondFile->unlink = true)
			->and($anOtherDirectory->readdir[3] = false)
			->and($workingDirectory->readdir[4] = $aFile = stream::getSubStream($workingDirectory))
			->and($aFile->unlink = true)
			->and($workingDirectory->readdir[5] = false)
			->and($svn = new \mock\mageekguy\atoum\scripts\builder\vcs\svn($adapter, $svnController = new mock\controller()))
			->and($svn->setWorkingDirectory('atoum://workingDirectory'))
			->then
				->object($svn->cleanWorkingDirectory())->isIdenticalTo($svn)
				->adapter($adapter)
					->call('unlink')->withArguments((string) $firstFile)->once()
					->call('unlink')->withArguments((string) $secondFile)->once()
					->call('rmdir')->withArguments((string) $aDirectory)->once()
					->call('rmdir')->withArguments((string) $emptyDirectory)->once()
					->call('unlink')->withArguments((string) $anOtherFirstFile)->once()
					->call('unlink')->withArguments((string) $anOtherSecondFile)->once()
					->call('rmdir')->withArguments((string) $anOtherDirectory)->once()
					->call('unlink')->withArguments((string) $aFile)->once()
					->call('rmdir')->withArguments((string) $workingDirectory)->never()
		;
	}
}
