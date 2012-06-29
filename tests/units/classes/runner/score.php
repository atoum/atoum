<?php

namespace mageekguy\atoum\tests\units\runner;

use
	mageekguy\atoum,
	mageekguy\atoum\runner\score as testedClass
;

require_once __DIR__ . '/../../runner.php';

class score extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubClassOf('mageekguy\atoum\score');
	}

	public function test__construct()
	{
		$this
			->if($score = new testedClass())
			->then
				->variable($score->getAtoumPath())->isNull()
				->variable($score->getAtoumVersion())->isNull()
				->variable($score->getPhpPath())->isNull()
				->variable($score->getPhpVersion())->isNull()
		;
	}

	public function testReset()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->reset())->isIdenticalTo($score)
				->variable($score->getAtoumPath())->isNull()
				->variable($score->getAtoumVersion())->isNull()
				->variable($score->getPhpPath())->isNull()
				->variable($score->getPhpVersion())->isNull()
			->if($score->setAtoumPath(uniqid()))
			->and($score->setAtoumVersion(uniqid()))
			->and($score->setPhpPath(uniqid()))
			->and($score->setPhpVersion(uniqid()))
			->then
				->object($score->reset())->isIdenticalTo($score)
				->variable($score->getAtoumPath())->isNull()
				->variable($score->getAtoumVersion())->isNull()
				->variable($score->getPhpPath())->isNull()
				->variable($score->getPhpVersion())->isNull()
		;
	}

	public function testSetAtoumPath()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->setAtoumPath($path = uniqid()))->isIdenticalTo($score)
				->string($score->getAtoumPath())->isEqualTo($path)
				->exception(function() use ($score) {
							$score->setAtoumPath(uniqid());
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Path of atoum is already set')
				->object($score->reset()->setAtoumPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->string($score->getAtoumPath())->isEqualTo((string) $path)
		;
	}

	public function testSetAtoumVersion()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->setAtoumVersion($version = uniqid()))->isIdenticalTo($score)
				->string($score->getAtoumVersion())->isEqualTo($version)
				->exception(function() use ($score) {
							$score->setAtoumVersion(uniqid());
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Version of atoum is already set')
				->object($score->reset()->setAtoumVersion($version = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->string($score->getAtoumVersion())->isEqualTo((string) $version)
		;
	}

	public function testSetPhpPath()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->setPhpPath($path = uniqid()))->isIdenticalTo($score)
				->string($score->getPhpPath())->isEqualTo($path)
				->exception(function() use ($score) {
							$score->setPhpPath(uniqid());
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('PHP path is already set')
				->object($score->reset()->setPhpPath($path = rand(1, PHP_INT_MAX)))->isIdenticalTo($score)
				->string($score->getPhpPath())->isEqualTo((string) $path)
		;
	}

	public function testSetPhpVersion()
	{
		$this
			->if($score = new testedClass())
			->then
				->object($score->setPhpVersion(\PHP_VERSION_ID))->isIdenticalTo($score)
				->string($score->getPhpVersion())->isEqualTo((string) \PHP_VERSION_ID)
				->exception(function() use ($score) {
						$score->setPhpVersion(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('PHP version is already set')
		;
	}
}
