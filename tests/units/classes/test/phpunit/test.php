<?php

namespace mageekguy\atoum\tests\units\test\phpunit;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\test\phpunit
;

require_once __DIR__ . '/../../../runner.php';

class dummy
{
	public function __construct($a, $b) {}
}

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

	public function testAssertTrueFalse()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->assertFalse(false))->isInstanceOf('\\mageekguy\\atoum\\asserters\\boolean')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertFalse(true);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->object($test->assertTrue(true))->isInstanceOf('\\mageekguy\\atoum\\asserters\\boolean')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertTrue(false);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
		;
	}

	public function testAssertNullNotNull()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->assertNull(null))->isInstanceOf('\\mageekguy\\atoum\\asserters\\variable')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(1)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertNull(uniqid());
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(0)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(1)
			->if($test->getScore()->reset())
			->then
				->object($test->assertNotNull(uniqid()))->isInstanceOf('\\mageekguy\\atoum\\asserters\\variable')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(1)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertNotNull(null);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(0)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(1)
		;
	}

	/**
	 * @dataProvider testAssertEqualsNotEqualsDataProvider
	 */
	public function testAssertEqualsNotEquals($value, $otherValue, $asserter, $passNumber)
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->assertEquals($value, $value))->isInstanceOf($asserter)
				->integer($test->getScore()->getPassNumber())->isEqualTo($passNumber)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo($passNumber)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test, $value, $otherValue) {
						$test->assertEquals($otherValue, $value);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo($passNumber - 1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo($passNumber)
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->assertNotEquals($otherValue, $value))->isInstanceOf($asserter)
				->integer($test->getScore()->getPassNumber())->isEqualTo($passNumber)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo($passNumber)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test, $value) {
						$test->assertNotEquals($value, $value);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo($passNumber - 1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo($passNumber)
		;
	}

	protected function testAssertEqualsNotEqualsDataProvider()
	{
		return array(
			array(
				null,
				uniqid(),
				'\\mageekguy\\atoum\\asserters\\variable',
				1,
				1
			),
			array(
				uniqid('_'),
				uniqid('_'),
				'\\mageekguy\\atoum\\asserters\\string',
				2,
				2
			),
			array(
				rand(0, PHP_INT_MAX),
				rand(0, PHP_INT_MAX),
				'\\mageekguy\\atoum\\asserters\\integer',
				2,
				2
			),
			array(
				microtime(true),
				microtime(true),
				'\\mageekguy\\atoum\\asserters\\float',
				2,
				2
			),
			array(
				array(uniqid() => uniqid()),
				array(uniqid() => uniqid()),
				'\\mageekguy\\atoum\\asserters\\phpArray',
				2,
				2
			),
			array(
				new \StdClass(),
				new self(),
				'\\mageekguy\\atoum\\asserters\\object',
				2,
				2
			),
			array(
				true,
				false,
				'\\mageekguy\\atoum\\asserters\\boolean',
				2,
				2
			)
		);
	}

	public function testAssertSame()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->assertSame($object = new \StdClass, $object))->isInstanceOf('\\mageekguy\\atoum\\asserters\\variable')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(1)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertContains(new \StdClass, new \StdClass);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(0)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(1)
		;
	}

	public function testAssertContainsNotContains()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->assertContains($needle = uniqid(), uniqid($needle)))->isInstanceOf('\\mageekguy\\atoum\\asserters\\string')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertContains(uniqid(), uniqid());
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->object($test->assertNotContains(uniqid(), uniqid()))->isInstanceOf('\\mageekguy\\atoum\\asserters\\string')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertNotContains($needle = uniqid(), uniqid($needle));
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
		;
	}

	public function testAssertInstanceOfNotInstanceOf()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->assertInstanceOf('\\StdClass', new \StdClass))->isInstanceOf('\\mageekguy\\atoum\\asserters\\object')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertInstanceOf($test, new \StdClass);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->object($test->assertNotInstanceOf(new self(), new \StdClass))->isInstanceOf('\\mageekguy\\atoum\\asserters\\object')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test) {
						$test->assertNotInstanceOf('\\StdClass', new \StdClass);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test, & $actual) {
						$test->assertInstanceOf(uniqid(), $actual = uniqid());
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
					->hasMessage('string(' . strlen($actual) . ') \'' . $actual . '\' is not an object')
				->exception(function() use ($test, & $actual) {
						$test->assertNotInstanceOf(uniqid(), $actual = uniqid());
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
					->hasMessage('string(' . strlen($actual) . ') \'' . $actual . '\' is not an object')
		;
	}

	public function testAssertArrayHasKey()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($actual = array($key = uniqid() => uniqid()))
			->then
				->object($test->assertArrayHasKey($key, $actual))->isInstanceOf('\\mageekguy\\atoum\\asserters\\phpArray')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test, $actual) {
						$test->assertArrayHasKey(uniqid(), $actual);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
				->integer($test->getScore()->getPassNumber())->isEqualTo(1)
				->integer($test->getScore()->getFailNumber())->isEqualTo(1)
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test, & $actual) {
						$test->assertArrayHasKey(uniqid(), $actual = uniqid());
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
					->hasMessage('string(' . strlen($actual) . ') \'' . $actual . '\' is not an array')
		;
	}

	public function testAssertCount()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($actual = array(uniqid(), uniqid()))
			->then
				->object($test->assertCount(count($actual), $actual))->isInstanceOf('\\mageekguy\\atoum\\asserters\\phpArray')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception($throwing = function() use ($test, & $actual) {
						$test->assertCount(rand(count($actual), PHP_INT_MAX), $actual);
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
			->if($test->getScore()->reset())
			->and($actual = new \ArrayIterator($actual))
			->then
				->object($test->assertCount(count($actual), $actual))->isInstanceOf('\\mageekguy\\atoum\\asserters\\object')
				->integer($test->getScore()->getPassNumber())->isEqualTo(2)
				->integer($test->getScore()->getFailNumber())->isZero()
				->integer($test->getScore()->getAssertionNumber())->isEqualTo(2)
			->if($test->getScore()->reset())
			->then
				->exception($throwing)->isInstanceOf('\\mageekguy\\atoum\\asserter\\exception')
			->if($test->getScore()->reset())
			->then
				->exception(function() use ($test, & $actual) {
						$test->assertCount(rand(0, PHP_INT_MAX), $actual = uniqid());
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\exceptions\\logic\\invalidArgument')
					->hasMessage('Value is not countable')
		;
	}

	public function testMarkTestSkipped()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($test->mockGenerator->setDefaultNamespace('mocking'))
			->and($mock = $test->getMock(__CLASS__))
			->and($otherMock = $test->getMock(__CLASS__))
			->and($definition = new \mock\mageekguy\atoum\test\phpunit\mock\definition($mock))
			->and($otherDefinition = new \mock\mageekguy\atoum\test\phpunit\mock\definition($otherMock))
			->and($this->calling($definition)->reset = $definition)
			->and($this->calling($mock)->getMockDefinition = $definition)
			->and($this->calling($otherDefinition)->reset = $otherDefinition)
			->and($this->calling($otherMock)->getMockDefinition = $otherDefinition)
			->then
				->exception(function() use ($test, & $message) {
						$test->markTestSkipped($message = uniqid());
					}
				)
					->isInstanceOf('\\mageekguy\\atoum\\test\\exceptions\\skip')
					->hasMessage($message)
				->mock($definition)->call('reset')->once()
				->mock($otherDefinition)->call('reset')->once()
				->mock($test)->call('skip')->withArguments($message)->once()
		;
	}

	public function testUnsupported()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->exception(function() use ($test) { $test->setExpectedException(uniqid()); })
					->isInstanceOf('\\mageekguy\\atoum\\test\\exceptions\\skip')
					->hasMessage('Testing exception is not available')
				->mock($test)->call('markTestSkipped')->once()
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->exception(function() use ($test) { $test->isInstanceOf(uniqid()); })
					->isInstanceOf('\\mageekguy\\atoum\\test\\exceptions\\skip')
					->hasMessage('isInstanceOf is not supported')
				->mock($test)->call('markTestSkipped')->once()
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->exception(function() use ($test) { $test->matchesRegularExpression(uniqid()); })
					->isInstanceOf('\\mageekguy\\atoum\\test\\exceptions\\skip')
					->hasMessage('matchesRegularExpression is not supported')
				->mock($test)->call('markTestSkipped')->once()
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->then
				->exception(function() use ($test) { $test->stringContains(uniqid()); })
					->isInstanceOf('\\mageekguy\\atoum\\test\\exceptions\\skip')
					->hasMessage('stringContains is not supported')
				->mock($test)->call('markTestSkipped')->once()
		;
	}

	public function testGetMock()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($test->mockGenerator->setDefaultNamespace('mocking'))
			->then
				->object($test->getMock(__CLASS__))->isInstanceOf(__CLASS__)
				->class('\\mocking\\' . __CLASS__)
					->isSubClassOf(__CLASS__)
					->hasInterface(('\\mageekguy\\atoum\\test\\phpunit\\mock\\aggregator'))
				->object($test->getMock('\\StdClass', array(), array(), 'mocked'))->isInstanceOf('\\StdClass')
					->class('\\mocking\\mocked')
					->isSubClassOf('\\StdClass')
					->hasInterface(('\\mageekguy\\atoum\\test\\phpunit\\mock\\aggregator'))
			->if($mock = $test->getMock(__NAMESPACE__ . '\\dummy', array(), array($arg = uniqid(), $otherArg = uniqid())))
			->then
				->mock($mock)->call('__construct')->withArguments($arg, $otherArg)->once()

		;
	}

	public function getMockBuilder()
	{
		$this
			->if($test = new \mock\mageekguy\atoum\test\phpunit\test())
			->and($test->mockGenerator->setDefaultNamespace('mocking'))
			->then
				->object($builder = $test->getMockBuilder(__CLASS__))->isInstanceOf('\\mageekguy\\atoum\\test\\phpunit\\mock\\builder')
				->object($test->getMockBuilder(__CLASS__))->isNotIdenticalTo($builder);
		;
	}
}
