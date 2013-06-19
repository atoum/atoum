<?php

namespace mageekguy\atoum\tests\units\test\phpunit;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\phpunit
;

require_once __DIR__ . '/../../../runner.php';

class test extends atoum\test
{
	public function testClass()
	{
		$this->testedClass
			->isSubClassOf('\\mageekguy\\atoum\\test')
		;
	}

	public function test__construct()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->getScore())->isInstanceOf('mageekguy\atoum\score')
				->object($test->getLocale())->isEqualTo(new atoum\locale())
				->object($test->getAdapter())->isEqualTo(new atoum\adapter())
				->boolean($test->isIgnored())->isTrue()
				->boolean($test->debugModeIsEnabled())->isFalse()
				->array($test->getMethodTags())->isEmpty()
				->array($test->getDataProviders())->isEmpty()
				->boolean($test->codeCoverageIsEnabled())->isEqualTo(extension_loaded('xdebug'))
				->string($test->getTestNamespace())->isEqualTo(phpunit\test::defaultNamespace)
				->variable($test->getBootstrapFile())->isNull()
				->array($test->getClassPhpVersions())->isEmpty()
				->array($test->getMandatoryClassExtensions())->isEmpty()
				->array($test->getMandatoryMethodExtensions())->isEmpty()
				->object($test->getMockGenerator())->isInstanceOf('\\mageekguy\\atoum\\test\\phpunit\\mock\\generator')
				->array($test->getUnsupportedMethods())->isEmpty()
		;
	}

	public function testGetMockGenerator()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->getMockGenerator())->isInstanceOf('\\mageekguy\\atoum\\test\\phpunit\\mock\\generator')
			->if($test->setMockGenerator($mockGenerator = new atoum\test\phpunit\mock\generator($this)))
			->then
				->object($test->getMockGenerator())->isIdenticalTo($mockGenerator)
				->object($mockGenerator->getTest())->isIdenticalTo($test)
		;
	}

	public function testAddGetUnsupportedMethods()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->array($test->getUnsupportedMethods())->isEmpty()
			->if($method = uniqid())
			->and($reason = uniqid())
			->then
				->object($test->addUnsupportedMethod($method, $reason))->isIdenticalTo($test)
				->array($test->getUnsupportedMethods())->isEqualTo(array($method => $reason))
			->if($test->addUnsupportedMethod($method, uniqid()))
			->then
				->array($test->getUnsupportedMethods())->isEqualTo(array($method => $reason))
		;
	}

	public function testAddGetMocks()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->array($test->getMocks())->isEmpty()
			->if($generator = new atoum\test\phpunit\mock\generator($test))
			->and($generator->generate($class = uniqid('_')))
			->and($mockClass = '\\' . $generator->getDefaultNamespace() . '\\' . $class)
			->and($mock = new $mockClass())
			->then
				->object($test->addMock($mock))->isIdenticalTo($test)
				->array($test->getMocks())->isEqualTo(array($mock))
			->and($generator->generate($class = 'StdClass'))
			->and($mockClass = '\\' . $generator->getDefaultNamespace() . '\\' . $class)
			->and($otherMock = new $mockClass())
			->then
				->object($test->addMock($otherMock))->isIdenticalTo($test)
				->array($test->getMocks())->isEqualTo(array($mock, $otherMock))
		;
	}
}
