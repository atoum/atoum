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

	public function testRelativizeFrom()
	{
		$this
			->if($path = new testedClass('/a/b', '/'))
			->then
				->object($path->relativizeFrom(clone $path))
					->isInstanceOf('mageekguy\atoum\fs\path')
					->toString->isEqualTo('.')
			->if($path = new testedClass('/a/b', '/'))
			->then
				->object($path->relativizeFrom(new testedClass('/a', '/')))
					->isInstanceOf('mageekguy\atoum\fs\path')
					->toString->isEqualTo('./b')
			->if($path = new testedClass('/a/b', '/'))
			->then
				->object($path->relativizeFrom(new testedClass('/a/', '/')))
					->isInstanceOf('mageekguy\atoum\fs\path')
					->toString->isEqualTo('./b')
			->if($path = new testedClass('/a/b', '/'))
			->then
				->object($path->relativizeFrom(new testedClass('/c', '/')))
					->isInstanceOf('mageekguy\atoum\fs\path')
					->toString->isEqualTo('../a/b')
			->if($path = new testedClass('/a/b', '/'))
			->then
				->object($path->relativizeFrom(new testedClass('/c/', '/')))
					->isInstanceOf('mageekguy\atoum\fs\path')
					->toString->isEqualTo('../a/b')
			->if($path = new testedClass('/a/b', '/'))
			->then
				->object($path->relativizeFrom(new testedClass('/c/d', '/')))
					->isInstanceOf('mageekguy\atoum\fs\path')
					->toString->isEqualTo('../../a/b')
			->if($path = new testedClass('/a/b', '/'))
			->then
				->object($path->relativizeFrom(new testedClass('/c/d/', '/')))
					->isInstanceOf('mageekguy\atoum\fs\path')
					->toString->isEqualTo('../../a/b')
			->if($path = new testedClass('/a/b', '/'))
			->then
				->object($path->relativizeFrom(new testedClass('/', '/')))
					->isInstanceOf('mageekguy\atoum\fs\path')
					->toString->isEqualTo('./a/b')
		;
	}

	public function testGetParentDirectory()
	{
		$this
			->if($path = new testedClass('/', '/'))
			->then
				->object($path->getParentDirectory())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('/')
			->if($path = new testedClass('/' . uniqid(), '/'))
			->then
				->object($path->getParentDirectory())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('/')
			->if($path = new testedClass(($parentDirectory = '/' . uniqid()) . '/' . uniqid(), '/'))
			->then
				->object($path->getParentDirectory())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo($parentDirectory)
				->object($path->getParentDirectory()->getParentDirectory())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('/')
			->if($path = new testedClass('\\', '\\'))
			->then
				->object($path->getParentDirectory())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('\\')
			->if($path = new testedClass('\\' . uniqid(), '\\'))
			->then
				->object($path->getParentDirectory())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('\\')
			->if($path = new testedClass('C:\\' . uniqid(), '\\'))
			->then
				->object($path->getParentDirectory())
					->isNotIdenticalTo($path)
					->toString
						->isEqualTo('C:\\')
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
				->boolean($path->isRoot())->isFalse()
			->if($path = new testedClass('C:\\', '\\'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('c:\\', '\\'))
			->then
				->boolean($path->isRoot())->isTrue()
			->if($path = new testedClass('C:\\', '/'))
			->then
				->boolean($path->isRoot())->isFalse()
			->if($path = new testedClass('C:\\' . uniqid(), '\\'))
			->then
				->boolean($path->isRoot())->isFalse()
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

	public function testAbsolutize()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->getcwd = $currentDirectory = uniqid())
			->then
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->object($path->absolutize())->isCloneOf($path)
				->if($path = new testedClass('../a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->castToString($path->absolutize())->isEqualTo($currentDirectory . DIRECTORY_SEPARATOR . $path)
				->if($path = new testedClass('a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->castToString($path->absolutize())->isEqualTo($currentDirectory . DIRECTORY_SEPARATOR . $path)
		;
	}

	public function testResolve()
	{
		$this
			->if($path = new testedClass('/a/b'))
			->then
				->object($path->resolve())->isEqualTo(new testedClass('/a/b'))
			->if($path = new testedClass('/a/b/..'))
			->then
				->object($path->resolve())->isEqualTo(new testedClass('/a'))
			->if($path = new testedClass('/a/b/../..'))
			->then
				->object($path->resolve())->isEqualTo(new testedClass('/'))
			->if($path = new testedClass('/a/b/.'))
			->then
				->object($path->resolve())->isEqualTo(new testedClass('/a/b'))
			->if($path = new testedClass('/a/./b'))
			->then
				->object($path->resolve())->isEqualTo(new testedClass('/a/b'))
			->if($path = new testedClass('//a////./////b'))
			->then
				->object($path->resolve())->isEqualTo(new testedClass('/a/b'))
		;
	}
}
