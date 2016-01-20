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
			->if($this->newTestedInstance(DIRECTORY_SEPARATOR))
			->then
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo(DIRECTORY_SEPARATOR)
				->castToString($this->testedInstance)->isEqualTo(DIRECTORY_SEPARATOR)

			->if($this->newTestedInstance($value = uniqid()))
			->then
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo(DIRECTORY_SEPARATOR)
				->castToString($this->testedInstance)->isEqualTo($value)

			->if($this->newTestedInstance($value, '\\'))
			->then
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo('\\')
				->castToString($this->testedInstance)->isEqualTo($value)

			->if($this->newTestedInstance($value, '/'))
			->then
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo('/')
				->castToString($this->testedInstance)->isEqualTo($value)

			->if($this->newTestedInstance($value . DIRECTORY_SEPARATOR))
			->then
				->castToString($this->testedInstance)->isEqualTo($value)
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo(DIRECTORY_SEPARATOR)

			->if($this->newTestedInstance($value . '/', '/'))
			->then
				->castToString($this->testedInstance)->isEqualTo($value)
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo('/')

			->if($this->newTestedInstance($value . '/', '\\'))
			->then
				->castToString($this->testedInstance)->isEqualTo($value)
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo('\\')

			->if($this->newTestedInstance($value . '\\', '\\'))
			->then
				->castToString($this->testedInstance)->isEqualTo($value)
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo('\\')

			->if($this->newTestedInstance($value, '\\'))
			->then
				->castToString($this->testedInstance)->isEqualTo($value)
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo('\\')

			->if($this->newTestedInstance('C:\\'))
			->then
				->castToString($this->testedInstance)->isEqualTo('C:' . DIRECTORY_SEPARATOR)
				->string($this->testedInstance->getDirectorySeparator())->isEqualTo(DIRECTORY_SEPARATOR)
		;
	}

	public function testExists()
	{
		$this
			->given($this->newTestedInstance(uniqid(), '/'))

			->if($this->function->file_exists = false)
			->then
				->boolean($this->testedInstance->exists())->isFalse()
				->function('file_exists')->wasCalledWithArguments((string) $this->testedInstance)->once()

			->if($this->function->file_exists = true)
			->then
				->boolean($this->testedInstance->exists())->isTrue()
				->function('file_exists')->wasCalledWithArguments((string) $this->testedInstance)->twice()
		;
	}

	public function testAbsolutize()
	{
		$this
			->given($this->function->getcwd = $currentDirectory = '/current/directory')

			->if($this->newTestedInstance('/a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR. 'b')

			->if($this->newTestedInstance('../a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('../../../a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('./a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance($this->testedInstance)
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'.'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
		;
	}

	public function testAbsolutizeWindows()
	{
		$this
			->given($this->function->getcwd = $currentDirectory = 'C:\current\directory')

			->if($this->newTestedInstance('C:\a\b', '\\'))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\a\b')

			->if($this->newTestedInstance('..\a\b', '\\'))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\current\directory\..\a\b')

			->if($this->newTestedInstance('..\..\..\a\b', '\\'))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\current\directory\..\..\..\a\b')

			->if($this->newTestedInstance('a\b', '\\'))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\current\directory\a\b')

			->if($this->newTestedInstance('.\a\b', '\\'))
			->then
				->object($this->testedInstance->absolutize())
					->isTestedInstance($this->testedInstance)
					->toString
						->isEqualTo('C:\current\directory\.\a\b')
		;
	}

	public function testGetAbsolutePath()
	{
		$this
			->given($this->function->getcwd = $currentDirectory = '/current/directory')

				->if($this->newTestedInstance('/a/b', DIRECTORY_SEPARATOR))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

				->if($this->newTestedInstance('../a/b', DIRECTORY_SEPARATOR))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

				->if($this->newTestedInstance('../../../a/b', DIRECTORY_SEPARATOR))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

				->if($this->newTestedInstance('a/b', DIRECTORY_SEPARATOR))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

				->if($this->newTestedInstance('./a/b', DIRECTORY_SEPARATOR))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'.'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
		;
	}

	public function testGetAbsolutePathWindows()
	{
		$this
			->given($this->function->getcwd = $currentDirectory = 'C:\current\directory')

				->if($this->newTestedInstance('C:\a\b', '\\'))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo('C:\a\b')

				->if($this->newTestedInstance('..\a\b', '\\'))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo('C:\current\directory\..\a\b')

				->if($this->newTestedInstance('..\..\..\a\b', '\\'))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo('C:\current\directory\..\..\..\a\b')

				->if($this->newTestedInstance('a\b', '\\'))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo('C:\current\directory\a\b')

				->if($this->newTestedInstance('.\a\b', '\\'))
				->then
					->object($this->testedInstance->getAbsolutePath())
						->isNotTestedInstance()
						->isInstanceOfTestedClass()
						->toString
							->isEqualTo('C:\current\directory\.\a\b')
		;
	}

	public function testResolve()
	{
		$this

			->if($this->newTestedInstance('/a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('/a/b/..', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a')

			->if($this->newTestedInstance('/a/b/../..', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR)

			->if($this->newTestedInstance('/a/b/.', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('/a/./b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('//a////./////b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->given($this->function->getcwd = '/current/directory')

			->if($this->newTestedInstance('a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('./a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('../a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('../../a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->resolve())
				->isTestedInstance()
				->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
		;
	}

	public function testResolveWindows()
	{
		$this

			->if($this->newTestedInstance('C:\a\b', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\a\b')

			->if($this->newTestedInstance('C:\a\b\..', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\a')

			->if($this->newTestedInstance('C:\a\b\..', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\\a')

			->if($this->newTestedInstance('C:\a\b\.', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\a\b')

			->if($this->newTestedInstance('C:\a\.\b', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\a\b')

			->if($this->newTestedInstance('C:\\\\a\\\\\\\\.\\\\\\\\\\b', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\a\b')

			->given($this->function->getcwd = 'C:\current\directory')

			->if($this->newTestedInstance('a\b', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\current\directory\a\b')

			->if($this->newTestedInstance('.\a\b', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\current\directory\a\b')

			->if($this->newTestedInstance('..\a\b', '\\'))
			->then
				->object($this->testedInstance->resolve())
					->isTestedInstance()
					->toString
						->isEqualTo('C:\current\a\b')

			->if($this->newTestedInstance('..\..\a\b', '\\'))
			->then
				->object($this->testedInstance->resolve())
				->isTestedInstance()
				->toString
						->isEqualTo('C:\a\b')
		;
	}

	public function testGetResolvedPath()
	{
		$this
			->if($this->newTestedInstance('/a/b'))
			->then
				->object($this->testedInstance->getResolvedPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('/a/b/..'))
			->then
				->object($this->testedInstance->getResolvedPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a')

			->if($this->newTestedInstance('/a/b/../..'))
			->then
				->object($this->testedInstance->getResolvedPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR)

			->if($this->newTestedInstance('/a/b/.'))
			->then
				->object($this->testedInstance->getResolvedPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('/a/./b'))
			->then
				->object($this->testedInstance->getResolvedPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('//a////./////b'))
			->then
				->object($this->testedInstance->getResolvedPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->given($this->function->getcwd = '/current/directory')

			->if($this->newTestedInstance('a/b', DIRECTORY_SEPARATOR))
			->then
					->object($this->testedInstance->getResolvedPath())
						->isNotIdenticalTo($this->testedInstance)
						->toString
							->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('./a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->getResolvedPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'directory'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('../a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->getResolvedPath())
				->isNotTestedInstance()
				->isInstanceOfTestedClass()
				->toString
					->isEqualTo(DIRECTORY_SEPARATOR.'current'.DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')

			->if($this->newTestedInstance('../../a/b', DIRECTORY_SEPARATOR))
			->then
				->object($this->testedInstance->getResolvedPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo(DIRECTORY_SEPARATOR.'a'.DIRECTORY_SEPARATOR.'b')
		;
	}

	public function testIsSubPathOf()
	{
		$this
			->given($reference = $this->newTestedInstance('/a/b', DIRECTORY_SEPARATOR))

			->if($this->newTestedInstance('/a/b/c/d/e/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

				->if($this->newTestedInstance('/a/b/c/d/e', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

				->if($this->newTestedInstance('/a/b/c/d', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

				->if($this->newTestedInstance('/a/b/c/d/', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

				->if($this->newTestedInstance('/a/b/c', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

				->if($this->newTestedInstance('/a/b/c/', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

				->if($this->newTestedInstance('/a/b/', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

				->if($this->newTestedInstance('/a/b', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

				->if($this->newTestedInstance('/a', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

				->if($this->newTestedInstance('/a/', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

				->if($this->newTestedInstance('/', DIRECTORY_SEPARATOR))
				->then
					->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

			->given($reference = $this->newTestedInstance('/', DIRECTORY_SEPARATOR))

			->if($this->newTestedInstance('/a/b/c/d/e/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d/e', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

			->given(
				$this->function->getcwd = '/a/b',
				$reference = $this->newTestedInstance('d/e/../..', DIRECTORY_SEPARATOR)
			)

			->if($this->newTestedInstance('/a/b/c/d/e/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d/e', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

			->if($this->newTestedInstance('/a/b', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

			->if($this->newTestedInstance('/a', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

			->if($this->newTestedInstance('/a/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

			->if($this->newTestedInstance('/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()

			->given($reference = $this->newTestedInstance('/', DIRECTORY_SEPARATOR))

			->if($this->newTestedInstance('/a/b/c/d/e/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d/e', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/d/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/c/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/b', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/a/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isTrue()

			->if($this->newTestedInstance('/', DIRECTORY_SEPARATOR))
			->then
				->boolean($this->testedInstance->isSubPathOf($reference))->isFalse()
		;
	}

	public function testIsRoot()
	{
		$this
			->if($this->newTestedInstance(DIRECTORY_SEPARATOR))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance('/', '/'))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance('/' . uniqid() , '/'))->boolean($this->testedInstance->isRoot())->isFalse()
			->if($this->newTestedInstance('/', '\\'))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance('\\', '\\'))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance('\\', '/'))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance('C:\\', '\\'))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance('c:\\', '\\'))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance('C:\\', '/'))->boolean($this->testedInstance->isRoot())->isTrue()
			->if($this->newTestedInstance('C:\\' . uniqid(), '\\'))->boolean($this->testedInstance->isRoot())->isFalse()
			->if($this->newTestedInstance('C:/' . uniqid()))->boolean($this->testedInstance->isRoot())->isFalse()
			->if($this->newTestedInstance('C:/' . uniqid(), DIRECTORY_SEPARATOR))->boolean($this->testedInstance->isRoot())->isFalse()
			->if($this->newTestedInstance('C:/' . uniqid(), '/'))->boolean($this->testedInstance->isRoot())->isFalse()
			->if($this->newTestedInstance('C:/' . uniqid() , '\\'))->boolean($this->testedInstance->isRoot())->isFalse()
		;
	}

	public function testRelativizeFrom($path, $directorySeparator, $fromPath, $fromDirectorySeparator, $relativePath)
	{
		$this
			->given($reference = $this->newTestedInstance($fromPath, $fromDirectorySeparator))
			->then
				->object($this->newTestedInstance($path, $directorySeparator)->relativizeFrom($reference))
					->isTestedInstance()
					->toString
						->isEqualTo($relativePath)
		;
	}

	public function testGetRelativePathFrom($path, $directorySeparator, $fromPath, $fromDirectorySeparator, $relativePath)
	{
		$this
			->given($reference = $this->newTestedInstance($fromPath, $fromDirectorySeparator))
			->then
				->object($this->newTestedInstance($path, $directorySeparator)->getRelativePathFrom($reference))
					->isNotTestedInstance($path)
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo($relativePath)
		;
	}

	public function testGetParentDirectoryPath()
	{
		$this
			->if($this->newTestedInstance('/', '/'))
			->then
				->object($this->testedInstance->getParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/')

			->if($this->newTestedInstance('/' . uniqid() , '/'))
			->then
				->object($this->testedInstance->getParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/')

			->if($this->newTestedInstance(($parentDirectory = '/' . uniqid()) . '/' . uniqid(), '/'))
			->then
				->object($this->testedInstance->getParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo($parentDirectory)
				->object($this->testedInstance->getParentDirectoryPath()->getParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/')

			->if($this->newTestedInstance('\\', '\\'))
			->then
				->object($this->testedInstance->getParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('\\')

			->if($this->newTestedInstance('\\' . uniqid() , '\\'))
			->then
				->object($this->testedInstance->getParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('\\')

			->if($this->newTestedInstance('C:\\' . uniqid() , '\\'))
			->then
				->object($this->testedInstance->getParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('C:\\')
		;
	}

	public function testGetRealPath()
	{
		$this
			->given($this->function->realpath = function($path) {
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

			->if($this->newTestedInstance('/a', '/'))
			->then
				->object($this->testedInstance->getRealPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/a')

			->if($this->newTestedInstance('/a/b/c', '/'))
			->then
				->object($this->testedInstance->getRealPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/x/y/z')

			->if($this->newTestedInstance('/a/b/c/d/e', '/'))
			->then
				->object($this->testedInstance->getRealPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/x/y/z/d/e')

			->if($path = $this->newTestedInstance('/an/invalid/path', '/'))
			->then
				->exception(function() use ($path) { $path->getRealPath(); })
					->isInstanceOf('mageekguy\atoum\fs\path\exception')
					->hasMessage('Unable to get real path for \'' . $this->testedInstance . '\'')
		;
	}

	public function testGetRealParentDirectoryPath()
	{
		$this
			->given($this->function->file_exists = function($path) {
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

			->if($this->newTestedInstance('/', '/'))
			->then
				->object($this->testedInstance->getRealParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/')

			->if($this->newTestedInstance('/a', '/'))
			->then
				->object($this->testedInstance->getRealParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/')

			->if($this->newTestedInstance('/a/', '/'))
			->then
				->object($this->testedInstance->getRealParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/')

			->if($this->newTestedInstance('/a/b', '/'))
			->then
				->object($this->testedInstance->getRealParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/a')

			->if($this->newTestedInstance('/a/b/', '/'))
			->then
				->object($this->testedInstance->getRealParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/a')

			->if($this->newTestedInstance('/a/b/c', '/'))
			->then
				->object($this->testedInstance->getRealParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/a/b')

			->if($this->newTestedInstance('/a/b/c/d', '/'))
			->then
				->object($this->testedInstance->getRealParentDirectoryPath())
					->isNotTestedInstance()
					->isInstanceOfTestedClass()
					->toString
						->isEqualTo('/a/b')
		;
	}

	public function testCreateParentDirectory()
	{
		$this
			->given($path = $this->newTestedInstance('/a/b', '/'))

			->if(
				$this->function->file_exists = false,
				$this->function->mkdir = true
			)
			->then
				->object($this->testedInstance->createParentDirectory())->isTestedInstance()
				->function('file_exists')->wasCalledWithArguments('/a')->once()
				->function('mkdir')->wasCalledWithArguments('/a', 0777, true)->once()

			->if($this->function->mkdir = false)
			->then
				->exception(function() use ($path) { $path->createParentDirectory(); })
					->isInstanceOf('mageekguy\atoum\fs\path\exception')
					->hasMessage('Unable to create directory \'/a\'')
				->function('file_exists')->wasCalledWithArguments('/a')->twice()
				->function('mkdir')->wasCalled()->twice()

			->if(
				$this->function->file_exists = true
			)
			->then
				->object($this->testedInstance->createParentDirectory())->isEqualTo($this->testedInstance)
				->function('file_exists')->wasCalledWithArguments('/a')->thrice()
				->function('mkdir')->wasCalled()->twice()
		;
	}

	public function testPutContents()
	{
		$this
			->given($path = $this->newTestedInstance('/a/b', '/'))

			->if(
				$this->function->mkdir = true,
				$this->function->file_put_contents = true
			)
			->then
				->object($this->testedInstance->putContents($data = uniqid()))->isTestedInstance()
				->function('mkdir')->wasCalledWithArguments('/a', true)->once()
				->function('file_put_contents')->wasCalledWithArguments((string) $this->testedInstance, $data)->once()

			->if($this->function->file_put_contents = false)
			->then
				->exception(function() use ($path, & $data) { $path->putContents($data = uniqid()); })
					->isInstanceOf('mageekguy\atoum\fs\path\exception')
					->hasMessage('Unable to put data \'' . $data . '\' in file \'' . $this->testedInstance . '\'')
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
			array('/a/b', '/', '/', '/', './a/b'),

			array('C:\a\b', '\\', 'C:\a\b', '\\', '.'),
			array('C:\a\b', '\\', 'C:\a', '\\', '.\b'),
			array('C:\a\b', '\\', 'C:\a\\', '\\', '.\b'),
			array('C:\a\b', '\\', 'C:\c', '\\', '..\a\b'),
			array('C:\a\b', '\\', 'C:\c\\', '\\', '..\a\b'),
			array('C:\a\b', '\\', 'C:\c\d', '\\', '..\..\a\b'),
			array('C:\a\b', '\\', 'C:\c\d\\', '\\', '..\..\a\b'),
			array('C:\a\b', '\\', 'C:\\', '\\', '.\a\b')
		);
	}

	protected function testGetRelativePathFromDataProvider()
	{
		return $this->testRelativizeFromDataProvider();
	}
}
