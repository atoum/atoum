<?php

namespace mageekguy\atoum\tests\units\fs;

require_once __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\fs\path as testedClass
;

// See http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247(v=vs.85).aspx for more informations
class path extends atoum\test
{
	public function test__construct()
	{
		$this
			->given($value = uniqid())
			->then
				->if($path = new testedClass(DIRECTORY_SEPARATOR))
				->then
					->string($path->getDirectorySeparator())->isEqualTo(DIRECTORY_SEPARATOR)
					->castToString($path)->isEqualTo(DIRECTORY_SEPARATOR)
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
				->if($path = new testedClass($value))
				->then
					->string($path->getDirectorySeparator())->isEqualTo(DIRECTORY_SEPARATOR)
					->castToString($path)->isEqualTo($value)
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
				->if($path = new testedClass($value, '\\'))
				->then
					->string($path->getDirectorySeparator())->isEqualTo('\\')
					->castToString($path)->isEqualTo($value)
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
				->if($path = new testedClass($value, '/'))
				->then
					->string($path->getDirectorySeparator())->isEqualTo('/')
					->castToString($path)->isEqualTo($value)
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
				->if($path = new testedClass($value . DIRECTORY_SEPARATOR))
				->then
					->castToString($path)->isEqualTo($value)
					->string($path->getDirectorySeparator())->isEqualTo(DIRECTORY_SEPARATOR)
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
				->if($path = new testedClass($value . '/', '/'))
				->then
					->castToString($path)->isEqualTo($value)
					->string($path->getDirectorySeparator())->isEqualTo('/')
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
				->if($path = new testedClass($value . '/', '\\'))
				->then
					->castToString($path)->isEqualTo($value)
					->string($path->getDirectorySeparator())->isEqualTo('\\')
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
				->if($path = new testedClass($value . '\\', '\\'))
				->then
					->castToString($path)->isEqualTo($value)
					->string($path->getDirectorySeparator())->isEqualTo('\\')
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
				->if($path = new testedClass($value, '\\', $adapter = new atoum\adapter()))
				->then
					->castToString($path)->isEqualTo($value)
					->string($path->getDirectorySeparator())->isEqualTo('\\')
					->object($path->getAdapter())->isIdenticalTo($adapter)
				->if($path = new testedClass('C:\\'))
				->then
					->castToString($path)->isEqualTo('C:' . DIRECTORY_SEPARATOR)
					->string($path->getDirectorySeparator())->isEqualTo(DIRECTORY_SEPARATOR)
					->object($path->getAdapter())->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($path = new testedClass('/'))
			->then
				->object($path->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($path)
				->object($path->getAdapter())->isIdenticalTo($adapter)
				->object($path->setAdapter())->isIdenticalTo($path)
				->object($path->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testExists()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($path = new testedClass(uniqid(), '/', $adapter))
			->then
				->if($adapter->file_exists = false)
				->then
					->boolean($path->exists())->isFalse()
					->adapter($adapter)->call('file_exists')->withArguments((string) $path)->once()
				->if($adapter->file_exists = true)
				->then
					->boolean($path->exists())->isTrue()
					->adapter($adapter)->call('file_exists')->withArguments((string) $path)->twice()
		;
	}

	public function testAbsolutize()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->getcwd = $currentDirectory = '/current/directory')
			->then
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->absolutize())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR. 'b')
				->if($path = new testedClass('../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->absolutize())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('../../../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->absolutize())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->absolutize())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('./a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->absolutize())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'.'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
		;
	}

	public function testGetAbsolutePath()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->getcwd = $currentDirectory = '/current/directory')
			->then
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getAbsolutePath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getAbsolutePath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('../../../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getAbsolutePath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getAbsolutePath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('./a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getAbsolutePath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'.'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
		;
	}

	public function testResolve()
	{
		$this
			->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR))
			->then
				->object($path->resolve())
					->isIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
			->if($path = new testedClass('/a/b/..', DIRECTORY_SEPARATOR))
			->then
				->object($path->resolve())
					->isIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a')
			->if($path = new testedClass('/a/b/../..', DIRECTORY_SEPARATOR))
			->then
				->object($path->resolve())
					->isIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR)
			->if($path = new testedClass('/a/b/.', DIRECTORY_SEPARATOR))
			->then
				->object($path->resolve())
					->isIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
			->if($path = new testedClass('/a/./b', DIRECTORY_SEPARATOR))
			->then
				->object($path->resolve())
					->isIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
			->if($path = new testedClass('//a////./////b', DIRECTORY_SEPARATOR))
			->then
				->object($path->resolve())
					->isIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
			->given($adapter = new atoum\test\adapter())
			->and($adapter->getcwd = $currentDirectory = '/current/directory')
			->then
				->if($path = new testedClass('a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->resolve())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('./a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->resolve())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->resolve())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('../../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->resolve())
						->isIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
		;
	}

	public function testGetResolvedPath()
	{
		$this
			->if($path = new testedClass('/a/b'))
			->then
				->object($path->getResolvedPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
			->if($path = new testedClass('/a/b/..'))
			->then
				->object($path->getResolvedPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a')
			->if($path = new testedClass('/a/b/../..'))
			->then
				->object($path->getResolvedPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR)
			->if($path = new testedClass('/a/b/.'))
			->then
				->object($path->getResolvedPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
			->if($path = new testedClass('/a/./b'))
			->then
				->object($path->getResolvedPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
			->if($path = new testedClass('//a////./////b'))
			->then
				->object($path->getResolvedPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
			->given($adapter = new atoum\test\adapter())
			->and($adapter->getcwd = $currentDirectory = '/current/directory')
			->then
				->if($path = new testedClass('a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getResolvedPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('./a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getResolvedPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getResolvedPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
				->if($path = new testedClass('../../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->getResolvedPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
		;
	}

	public function testIsSubPathOf()
	{
		$this
			->if($reference = new testedClass('/a/b', DIRECTORY_SEPARATOR))
			->then
				->if($path = new testedClass('/a/b/c/d/e/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/e', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
			->given($reference = new testedClass('/', DIRECTORY_SEPARATOR))
			->then
				->if($path = new testedClass('/a/b/c/d/e/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/e', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
			->given($referenceAdapter = new atoum\test\adapter())
			->and($referenceAdapter->getcwd = '/a/b')
			->and($reference = new testedClass('d/e/../..', DIRECTORY_SEPARATOR, $referenceAdapter))
			->then
				->if($path = new testedClass('/a/b/c/d/e/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/e', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
			->given($reference = new testedClass('/', DIRECTORY_SEPARATOR))
			->then
				->if($path = new testedClass('/a/b/c/d/e/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/e', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/', DIRECTORY_SEPARATOR))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
		;
	}

	public function testIsRoot()
	{
		$this
			->if($path = new testedClass(DIRECTORY_SEPARATOR))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('/', '/'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('/' . uniqid(), '/'))
			->then
				->boolean($path->isRoot())->isFalse()
			->if($path = new testedClass('/', '\\'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('\\', '\\'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('\\', '/'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('C:\\', '\\'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('c:\\', '\\'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('C:\\', '/'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('C:\\' . uniqid(), '\\'))
			->then
				->boolean($path->isRoot())->isFalse()
			->if($path = new testedClass('C:/' . uniqid()))
			->then
				->boolean($path->isRoot())->isFalse()
			->if($path = new testedClass('C:/' . uniqid(), DIRECTORY_SEPARATOR))
			->then
				->boolean($path->isRoot())->isFalse()
			->if($path = new testedClass('C:/' . uniqid(), '/'))
			->then
				->boolean($path->isRoot())->isFalse()
			->if($path = new testedClass('C:/' . uniqid(), '\\'))
			->then
				->boolean($path->isRoot())->isFalse()
		;
	}

	public function testRelativizeFrom($path, $directorySeparator, $fromPath, $fromDirectorySeparator, $relativePath)
	{
		$this
			->if($path = new testedClass($path, $directorySeparator))
			->then
				->object($path->relativizeFrom(new testedClass($fromPath, $fromDirectorySeparator)))
					->isIdenticalTo($path)
					->toString->isEqualTo($relativePath)
		;
	}

	public function testGetRelativePathFrom($path, $directorySeparator, $fromPath, $fromDirectorySeparator, $relativePath)
	{
		$this
			->if($path = new testedClass($path, $directorySeparator))
			->then
				->object($path->getRelativePathFrom(new testedClass($fromPath, $fromDirectorySeparator)))
					->isNotIdenticalTo($path)
					->toString->isEqualTo($relativePath)
		;
	}

	public function testGetParentDirectoryPath()
	{
		$this
			->if($path = new testedClass('/', '/'))
			->then
				->object($path->getParentDirectoryPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('/')
			->if($path = new testedClass('/' . uniqid(), '/'))
			->then
				->object($path->getParentDirectoryPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('/')
			->if($path = new testedClass(($parentDirectory = '/' . uniqid()) . '/' . uniqid(), '/'))
			->then
				->object($path->getParentDirectoryPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo($parentDirectory)
				->object($path->getParentDirectoryPath()->getParentDirectoryPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('/')
			->if($path = new testedClass('\\', '\\'))
			->then
				->object($path->getParentDirectoryPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('\\')
			->if($path = new testedClass('\\' . uniqid(), '\\'))
			->then
				->object($path->getParentDirectoryPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('\\')
			->if($path = new testedClass('C:\\' . uniqid(), '\\'))
			->then
				->object($path->getParentDirectoryPath())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('C:\\')
		;
	}

	public function testGetRealPath()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->realpath = function($path) {
					switch ($path)
					{
						case '/an/invalid/path':
						case '/an/invalid':
						case '/an':
						case '/':
							return false;

						case '/a/b/c/d/e':
						case '/a/b/c/d':
							return false;

						case '/a/b/c':
							return '/x/y/z';

						default:
							return $path;
					}
				}
			)
			->then
				->if($path = new testedClass('/a', '/', $adapter))
				->then
					->object($path->getRealPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/a')
				->if($path = new testedClass('/a/b/c', '/', $adapter))
				->then
					->object($path->getRealPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/x/y/z')
				->if($path = new testedClass('/a/b/c/d/e', '/', $adapter))
				->then
					->object($path->getRealPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/x/y/z/d/e')
				->if($path = new testedClass('/an/invalid/path', '/', $adapter))
				->then
					->exception(function() use ($path) { $path->getRealPath(); })
						->isInstanceOf('mageekguy\atoum\fs\path\exception')
						->hasMessage('Unable to get real path for \'' . $path . '\'')
		;
	}

	public function testGetRealParentDirectoryPath()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->file_exists = function($path) {
					switch ($path)
					{
						case '/a/b/c/d/e':
						case '/a/b/c/d':
						case '/a/b/c':
							return false;

						default:
							return true;
					}
				}
			)
			->then
				->if($path = new testedClass('/', '/', $adapter))
				->then
					->object($path->getRealParentDirectoryPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/')
				->if($path = new testedClass('/a', '/', $adapter))
				->then
					->object($path->getRealParentDirectoryPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/')
				->if($path = new testedClass('/a/', '/', $adapter))
				->then
					->object($path->getRealParentDirectoryPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/')
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->getRealParentDirectoryPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/a')
				->if($path = new testedClass('/a/b/', '/', $adapter))
				->then
					->object($path->getRealParentDirectoryPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/a')
				->if($path = new testedClass('/a/b/c', '/', $adapter))
				->then
					->object($path->getRealParentDirectoryPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/a/b')
				->if($path = new testedClass('/a/b/c/d', '/', $adapter))
				->then
					->object($path->getRealParentDirectoryPath())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/a/b')
		;
	}

	public function testCreateParentDirectory()
	{
		$this
			->if($path = new testedClass('/a/b', '/'))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->file_exists = false)
			->and($adapter->mkdir = true)
			->and($path->setAdapter($adapter))
			->then
				->object($path->createParentDirectory())->isEqualTo($path)
				->adapter($adapter)->call('mkdir')->withArguments('/a', 0777, true)->once()
			->if($adapter->mkdir = false)
			->then
				->exception(function() use ($path) { $path->createParentDirectory(); })
					->isInstanceOf('mageekguy\atoum\fs\path\exception')
					->hasMessage('Unable to create directory \'/a\'')
			->if($adapter->file_exists = true)
			->and($this->resetAdapter($adapter))
			->then
				->object($path->createParentDirectory())->isEqualTo($path)
				->adapter($adapter)->call('mkdir')->never()
		;
	}

	public function testPutContents()
	{
		$this
			->if($path = new testedClass('/a/b', '/'))
			->and($adapter = new atoum\test\adapter())
			->and($adapter->mkdir = true)
			->and($adapter->file_put_contents = true)
			->and($path->setAdapter($adapter))
			->then
				->object($path->putContents($data = uniqid()))->isEqualTo($path)
				->adapter($adapter)
					->call('mkdir')->withArguments('/a', true)->once()
					->call('file_put_contents')->withArguments((string) $path, $data)->once()
			->if($adapter->file_put_contents = false)
			->then
				->exception(function() use ($path, & $data) { $path->putContents($data = uniqid()); })
					->isInstanceOf('mageekguy\atoum\fs\path\exception')
					->hasMessage('Unable to put data \'' . $data . '\' in file \'' . $path . '\'')
		;
	}

	protected function testRelativizeFromDataProvider()
	{
		return array(
			array('/a/b', '/', '/a/b', '/', '.'),
			array('/a/b', '/', '/a', '/', './b'),
			array('/a/b', '/', '/a/', '/', './b'),
			array('/a/b', '/', '/c', '/', '../a/b'),
			array('/a/b', '/', '/c/', '/', '../a/b'),
			array('/a/b', '/', '/c/d', '/', '../../a/b'),
			array('/a/b', '/', '/c/d/', '/', '../../a/b'),
			array('/a/b', '/', '/', '/', './a/b')
		);
	}

	protected function testGetRelativePathFromDataProvider()
	{
		return $this->testRelativizeFromDataProvider();
	}
}
