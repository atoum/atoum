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
			->given($adapter = new atoum\test\adapter())
			->and($adapter->realpath = function($path) { return $path; })
			->then
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->relativizeFrom(clone $path))
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo('.')
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->relativizeFrom(new testedClass('/a', '/', $adapter)))
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo('./b')
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->relativizeFrom(new testedClass('/a/', '/', $adapter)))
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo('./b')
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->relativizeFrom(new testedClass('/c', '/', $adapter)))
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo('../a/b')
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->relativizeFrom(new testedClass('/c/', '/', $adapter)))
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo('../a/b')
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->relativizeFrom(new testedClass('/c/d', '/', $adapter)))
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo('../../a/b')
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->relativizeFrom(new testedClass('/c/d/', '/', $adapter)))
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo('../../a/b')
				->if($path = new testedClass('/a/b', '/', $adapter))
				->then
					->object($path->relativizeFrom(new testedClass('/', '/', $adapter)))
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo('./a/b')
		;
	}

	public function testGetParentDirectory()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->realpath = function($path) { return $path; })
			->then
				->if($path = new testedClass('/', '/', $adapter))
				->then
					->object($path->getParentDirectory())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/')
				->if($path = new testedClass('/' . uniqid(), '/', $adapter))
				->then
					->object($path->getParentDirectory())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/')
				->if($path = new testedClass(($parentDirectory = '/' . uniqid()) . '/' . uniqid(), '/', $adapter))
				->then
					->object($path->getParentDirectory())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo($parentDirectory)
					->object($path->getParentDirectory()->getParentDirectory())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('/')
				->if($path = new testedClass('\\', '\\', $adapter))
				->then
					->object($path->getParentDirectory())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('\\')
				->if($path = new testedClass('\\' . uniqid(), '\\', $adapter))
				->then
					->object($path->getParentDirectory())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('\\')
				->if($path = new testedClass('C:\\' . uniqid(), '\\', $adapter))
				->then
					->object($path->getParentDirectory())
						->isNotIdenticalTo($path)
						->toString
							->isEqualTo('C:\\')
		;
	}

	public function testResolve()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->realpath = $currentWorkingDirectory = '/a/b/c/d')
			->then
				->if($path = new testedClass(uniqid(), '/'))
				->and($path->setAdapter($adapter))
				->then
					->object($path->resolve())
						->isInstanceOf('mageekguy\atoum\fs\path')
						->toString->isEqualTo($currentWorkingDirectory)
			->if($adapter->realpath = false)
			->then
				->if($path = new testedClass(uniqid(), '/'))
				->and($path->setAdapter($adapter))
				->then
					->exception(function() use ($path) { $path->resolve(); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Unable to resolve \'' . $path . '\'')
		;
	}

	public function testIsRoot()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->realpath = function($path) { return $path; })
			->then
				->if($path = new testedClass(DIRECTORY_SEPARATOR))
				->and($path->setAdapter($adapter))
				->then
					->boolean($path->isRoot())->isTrue()
				->if($path = new testedClass(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isRoot())->isTrue()
				->if($path = new testedClass('/', '/', $adapter))
				->then
					->boolean($path->isRoot())->isTrue()
				->if($path = new testedClass('/' . uniqid(), '/', $adapter))
				->then
					->boolean($path->isRoot())->isFalse()
				->if($path = new testedClass('/', '\\', $adapter))
				->then
					->boolean($path->isRoot())->isTrue()
				->if($path = new testedClass('\\', '\\', $adapter))
				->then
					->boolean($path->isRoot())->isTrue()
				->if($path = new testedClass('\\', '/', $adapter))
				->then
					->boolean($path->isRoot())->isFalse()
				->if($path = new testedClass('C:\\', '\\', $adapter))
				->then
					->boolean($path->isRoot())->isTrue()
				->if($path = new testedClass('c:\\', '\\', $adapter))
				->then
					->boolean($path->isRoot())->isTrue()
				->if($path = new testedClass('C:\\', '/', $adapter))
				->then
					->boolean($path->isRoot())->isFalse()
				->if($path = new testedClass('C:\\' . uniqid(), '\\', $adapter))
				->then
					->boolean($path->isRoot())->isFalse()
		;
	}

	public function testIsSubPathOf()
	{
		$this
			->given($adapter = new atoum\test\adapter())
			->and($adapter->realpath = function($path) { return $path; })
			->and($reference = new testedClass('/a/b', DIRECTORY_SEPARATOR, $adapter))
			->then
				->if($path = new testedClass('/a/b/c/d/e/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/e', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
			->given($reference = new testedClass('/', DIRECTORY_SEPARATOR, $adapter))
			->then
				->if($path = new testedClass('/a/b/c/d/e/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/e', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
			->given($referenceAdapter = new atoum\test\adapter())
			->and($referenceAdapter->realpath = '/a/b')
			->and($reference = new testedClass('d/e/../..', DIRECTORY_SEPARATOR, $referenceAdapter))
			->then
				->if($path = new testedClass('/a/b/c/d/e/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/e', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/a/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
				->if($path = new testedClass('/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
			->given($reference = new testedClass('/', DIRECTORY_SEPARATOR, $adapter))
			->then
				->if($path = new testedClass('/a/b/c/d/e/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/e', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/d/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/c/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/b', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/a/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isTrue()
				->if($path = new testedClass('/', DIRECTORY_SEPARATOR, $adapter))
				->then
					->boolean($path->isSubPathOf($reference))->isFalse()
		;
	}
}
