<?php

namespace mageekguy\atoum\tests\units\test;

use
	mageekguy\atoum,
	mageekguy\atoum\fs\path,
	mageekguy\atoum\template,
	mageekguy\atoum\test\generator as testedClass
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($generator = new testedClass())
			->then
				->string($generator->getTemplatesDirectory())->isEqualTo(atoum\directory . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'test' . DIRECTORY_SEPARATOR . 'generator')
				->variable($generator->getTestedClassesDirectory())->isNull()
				->variable($generator->getTestClassesDirectory())->isNull()
				->variable($generator->getRunnerPath())->isNull()
				->variable($generator->getTestedClassNamespace())->isNull()
				->object($generator->getTemplateParser())->isEqualTo(new template\parser())
				->object($generator->getPathFactory())->isEqualTo(new path\factory())
				->object($generator->getAdapter())->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetTemplatesDirectory()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setTemplatesDirectory($directory = uniqid()))->isIdenticalTo($generator)
				->string($generator->getTemplatesDirectory())->isEqualTo($directory)
		;
	}

	public function testSetTestedClassesDirectory()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setTestedClassesDirectory($directory = uniqid()))->isIdenticalTo($generator)
				->string($generator->getTestedClassesDirectory())->isEqualTo($directory . DIRECTORY_SEPARATOR)
				->object($generator->setTestedClassesDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($generator)
				->string($generator->getTestedClassesDirectory())->isEqualTo($directory . DIRECTORY_SEPARATOR)
		;
	}

	public function testSetTestClassesDirectory()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setTestClassesDirectory($directory = uniqid()))->isIdenticalTo($generator)
				->string($generator->getTestClassesDirectory())->isEqualTo($directory . DIRECTORY_SEPARATOR)
				->object($generator->setTestClassesDirectory(($directory = uniqid()) . DIRECTORY_SEPARATOR))->isIdenticalTo($generator)
				->string($generator->getTestClassesDirectory())->isEqualTo($directory . DIRECTORY_SEPARATOR)
		;
	}

	public function testSetRunnerPath()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setRunnerPath($path = uniqid()))->isIdenticalTo($generator)
				->string($generator->getRunnerPath())->isEqualTo($path)
		;
	}

	public function testSetTemplateParser()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setTemplateParser($templateParser = new template\parser()))->isIdenticalTo($generator)
				->object($generator->getTemplateParser())->isIdenticalTo($templateParser)
				->object($generator->setTemplateParser())->isIdenticalTo($generator)
				->object($generator->getTemplateParser())
					->isNotIdenticalTo($templateParser)
					->isEqualTo(new template\parser())
		;
	}

	public function testSetPathFactory()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setPathFactory($factory = new path\factory()))->isIdenticalTo($generator)
				->object($generator->getPathFactory())->isIdenticalTo($factory)
				->object($generator->setPathFactory())->isIdenticalTo($generator)
				->object($generator->getPathFactory())
					->isNotIdenticalTo($factory)
					->isEqualTo(new path\factory())
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($generator)
				->object($generator->getAdapter())->isIdenticalTo($adapter)
				->object($generator->setAdapter())->isIdenticalTo($generator)
				->object($generator->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testSetTestedClassNamespace()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setTestedClassNamespace($namespace = uniqid()))->isIdenticalTo($generator)
				->string($generator->getTestedClassNamespace())->isEqualTo($namespace . '\\')
				->object($generator->setTestedClassNamespace('\\' . ($namespace = uniqid()) . '\\'))->isIdenticalTo($generator)
				->string($generator->getTestedClassNamespace())->isEqualTo($namespace . '\\')
				->object($generator->setTestedClassNamespace('\\' . ($namespace = uniqid())))->isIdenticalTo($generator)
				->string($generator->getTestedClassNamespace())->isEqualTo($namespace . '\\')
		;
	}

	public function testSetTestClassNamespace()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setTestClassNamespace($namespace = uniqid()))->isIdenticalTo($generator)
				->string($generator->getTestClassNamespace())->isEqualTo($namespace . '\\')
				->object($generator->setTestClassNamespace('\\' . ($namespace = uniqid()) . '\\'))->isIdenticalTo($generator)
				->string($generator->getTestClassNamespace())->isEqualTo($namespace . '\\')
				->object($generator->setTestClassNamespace('\\' . ($namespace = uniqid())))->isIdenticalTo($generator)
				->string($generator->getTestClassNamespace())->isEqualTo($namespace . '\\')
		;
	}

	public function testSetFullyQualifiedTestClassNameExtractor()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setFullyQualifiedTestClassNameExtractor($extractor = function() {}))->isIdenticalTo($generator)
				->object($generator->getFullyQualifiedTestClassNameExtractor())->isIdenticalTo($extractor)
		;
	}

	public function testSetFullyQualifiedTestedClassNameExtractor()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setFullyQualifiedTestedClassNameExtractor($extractor = function() {}))->isIdenticalTo($generator)
				->object($generator->getFullyQualifiedTestedClassNameExtractor())->isIdenticalTo($extractor)
		;
	}

	public function testSetTestedClassPathExtractor()
	{
		$this
			->if($generator = new testedClass())
			->then
				->object($generator->setTestedClassPathExtractor($extractor = function() {}))->isIdenticalTo($generator)
				->object($generator->getTestedClassPathExtractor())->isIdenticalTo($extractor)
		;
	}

	public function testGenerate()
	{
		$this
			->if($generator = new testedClass())
			->and($generator->setAdapter($adapter = new atoum\test\adapter()))
			->and($generator->setPathFactory($pathFactory = new \mock\mageekguy\atoum\fs\path\factory()))
			->and($generator->setTemplateParser($templateParser = new \mock\mageekguy\atoum\template\parser()))
			->then
				->exception(function() use ($generator) { $generator->generate(uniqid()); })
					->isInstanceOf('mageekguy\atoum\test\generator\exception')
					->hasMessage('Tested classes directory is undefined')
			->if($generator->setTestedClassesDirectory($classesDirectory = uniqid()))
			->then
				->exception(function() use ($generator) { $generator->generate(uniqid()); })
					->isInstanceOf('mageekguy\atoum\test\generator\exception')
					->hasMessage('Tests directory is undefined')
			->if($generator->setTestClassesDirectory($testsDirectory = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('a', 'b', 'c'))))
			->then
				->exception(function() use ($generator) { $generator->generate(uniqid()); })
					->isInstanceOf('mageekguy\atoum\test\generator\exception')
					->hasMessage('Tested class namespace is undefined')
			->if($generator->setTestedClassNamespace($testedClassNamespace = uniqid()))
			->then
				->exception(function() use ($generator) { $generator->generate(uniqid()); })
					->isInstanceOf('mageekguy\atoum\test\generator\exception')
					->hasMessage('Test class namespace is undefined')
			->if($generator->setTestClassNamespace($testClassNamespace = uniqid()))
			->and($testClassesDirectoryPath = new \mock\mageekguy\atoum\fs\path(DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('a', 'b', 'c'))))
			->and($this->calling($testClassesDirectoryPath)->exists = true)
			->and($this->calling($testClassesDirectoryPath)->getRealPath = $testClassesDirectoryPath)
			->and($testedClassPath = new \mock\mageekguy\atoum\fs\path(DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('x', 'y', 'z', 'f.php'))))
			->and($this->calling($testedClassPath)->putContents = $testedClassPath)
			->and($testClassPath = new \mock\mageekguy\atoum\fs\path(DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('a', 'b', 'c', 'd', 'e', 'f.php'))))
			->and($this->calling($testClassPath)->getRealParentDirectoryPath = new \mock\mageekguy\atoum\fs\path(DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('a', 'b', 'c', 'd', 'e'))))
			->and($this->calling($testClassPath)->getRealPath = $testClassPath)
			->and($this->calling($testClassPath)->putContents = $testClassPath)
			->and($this->calling($pathFactory)->build = function($path) use ($testClassesDirectoryPath, $testClassPath, $testedClassPath) {
					switch ($path)
					{
						case (string) $testClassesDirectoryPath . DIRECTORY_SEPARATOR:
							return $testClassesDirectoryPath;

						case (string) $testClassPath:
							return $testClassPath;

						default:
							return $testedClassPath;
					}
				}
			)
			->then
				->object($generator->generate((string) $testClassPath))->isIdenticalTo($generator)
				->mock($templateParser)
					->call('parseFile')->withArguments($generator->getTemplatesDirectory() . DIRECTORY_SEPARATOR . 'testClass.php')->once()
		;
	}
}
