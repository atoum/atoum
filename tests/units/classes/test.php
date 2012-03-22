<?php

namespace mageekguy\atoum
{
	class emptyTest {}
	class notEmptyTest {}
}

namespace mageekguy\atoum\mock\mageekguy\atoum
{
	class test {}
}

namespace mageekguy\atoum\tests\units
{
	use
		mageekguy\atoum,
		mageekguy\atoum\mock
	;

	require_once __DIR__ . '/../runner.php';

	/**
	@ignore on
	@tags empty fake dummy
	*/
	class emptyTest extends atoum\test {}

	/**
	@ignore on
	*/
	class notEmptyTest extends atoum\test
	{
		/**
		@tags test method one method
		*/
		public function testMethod1() {}

		/**
		@ignore off
		@tags test method two
		*/
		public function testMethod2() {}

		public function aDataProvider()
		{
		}
	}

	class foo extends atoum\test
	{
		public function __construct()
		{
			$this->setTestedClassName('mageekguy\atoum\test');

			parent::__construct();
		}
	}

	class test extends atoum\test
	{
		public function testClass()
		{
			$this->assert
				->testedClass->hasInterface('mageekguy\atoum\adapter\aggregator')
			;
		}

		public function testClassConstants()
		{
			$this->assert
				->string(atoum\test::testMethodPrefix)->isEqualTo('test')
				->string(atoum\test::runStart)->isEqualTo('testRunStart')
				->string(atoum\test::beforeSetUp)->isEqualTo('beforeTestSetUp')
				->string(atoum\test::afterSetUp)->isEqualTo('afterTestSetUp')
				->string(atoum\test::beforeTestMethod)->isEqualTo('beforeTestMethod')
				->string(atoum\test::fail)->isEqualTo('testAssertionFail')
				->string(atoum\test::error)->isEqualTo('testError')
				->string(atoum\test::uncompleted)->isEqualTo('testUncompleted')
				->string(atoum\test::exception)->isEqualTo('testException')
				->string(atoum\test::success)->isEqualTo('testAssertionSuccess')
				->string(atoum\test::afterTestMethod)->isEqualTo('afterTestMethod')
				->string(atoum\test::beforeTearDown)->isEqualTo('beforeTestTearDown')
				->string(atoum\test::afterTearDown)->isEqualTo('afterTestTearDown')
				->string(atoum\test::runStop)->isEqualTo('testRunStop')
				->string(atoum\test::defaultNamespace)->isEqualTo('#(?:^|\\\\)tests?\\\\units?\\\\#i')
			;
		}

		public function test__construct()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->getScore())->isEqualTo(new atoum\score())
				->object($test->getLocale())->isEqualTo(new atoum\locale())
				->object($test->getAdapter())->isEqualTo(new atoum\adapter())
				->object($test->getSuperglobals())->isEqualTo(new atoum\superglobals())
				->boolean($test->isIgnored())->isTrue()
				->array($test->getAllTags())->isEqualTo($tags = array('empty', 'fake', 'dummy'))
				->array($test->getTags())->isEqualTo($tags)
				->array($test->getMethodTags())->isEmpty()
				->array($test->getDataProviders())->isEmpty()
				->boolean($test->codeCoverageIsEnabled())->isEqualTo(extension_loaded('xdebug'))
				->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
				->variable($test->getMaxChildrenNumber())->isNull()
				->variable($test->getBootstrapFile())->isNull()
			;

			$adapter = new atoum\test\adapter();
			$adapter->extension_loaded = true;

			$test = new emptyTest(null, null, $adapter);

			$this->assert
				->object($test->getScore())->isEqualTo(new atoum\score())
				->object($test->getLocale())->isEqualTo(new atoum\locale())
				->object($test->getAdapter())->isIdenticalTo($adapter)
				->object($test->getSuperglobals())->isEqualTo(new atoum\superglobals())
				->boolean($test->isIgnored())->isTrue()
				->array($test->getAllTags())->isEqualTo($tags = array('empty', 'fake', 'dummy'))
				->array($test->getTags())->isEqualTo($tags)
				->array($test->getMethodTags())->isEmpty()
				->array($test->getDataProviders())->isEmpty()
				->boolean($test->codeCoverageIsEnabled())->isTrue()
				->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
				->variable($test->getMaxChildrenNumber())->isNull()
			;

			$test = new emptyTest($score = new atoum\score(), $locale = new atoum\locale(), $adapter, $superglobals = new atoum\superglobals());

			$this->assert
				->object($test->getScore())->isIdenticalTo($score)
				->object($test->getLocale())->isIdenticalTo($locale)
				->object($test->getAdapter())->isIdenticalTo($adapter)
				->object($test->getSuperglobals())->isIdenticalTo($superglobals)
				->boolean($test->isIgnored())->isTrue()
				->array($test->getAllTags())->isEqualTo($tags = array('empty', 'fake', 'dummy'))
				->array($test->getTags())->isEqualTo($tags)
				->array($test->getMethodTags())->isEmpty()
				->array($test->getDataProviders())->isEmpty()
				->boolean($test->codeCoverageIsEnabled())->isTrue()
				->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
				->variable($test->getMaxChildrenNumber())->isNull()
			;

			$test = new notEmptyTest($score, $locale, $adapter);

			$this->assert
				->object($test->getScore())->isIdenticalTo($score)
				->object($test->getLocale())->isIdenticalTo($locale)
				->object($test->getAdapter())->isIdenticalTo($adapter)
				->object($test->getSuperglobals())->isInstanceOf('mageekguy\atoum\superglobals')
				->boolean($test->isIgnored())->isTrue()
				->array($test->getAllTags())->isEqualTo(array('test', 'method', 'one', 'two'))
				->array($test->getTags())->isEmpty()
				->array($test->getMethodTags())->isEqualTo(array(
						'testMethod1' => array('test', 'method', 'one'),
						'testMethod2' => array('test', 'method', 'two')
					)
				)
				->array($test->getDataProviders())->isEmpty()
				->boolean($test->codeCoverageIsEnabled())->isTrue()
				->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
				->variable($test->getMaxChildrenNumber())->isNull()
			;
		}

		public function test__toString()
		{
			$this->assert->castToString($this)->isEqualTo(__CLASS__);
		}

		public function test__get()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->assert)->isInstanceOf('mageekguy\atoum\asserter\generator')
				->object($test->define)->isInstanceOf('mageekguy\atoum\asserter\generator')
				->object($test->mockGenerator)->isInstanceOf('mageekguy\atoum\mock\generator')
			;

			$test->setMockGenerator($mockGenerator = new mock\generator());

			$this->assert
				->object($test->mockGenerator)->isIdenticalTo($mockGenerator)
			;

			$test->setAsserterGenerator($asserterGenerator = new atoum\asserter\generator(new emptyTest()));

			$this->assert
				->object($test->assert)->isIdenticalTo($asserterGenerator)
			;

			$this->assert
				->exception(function() use ($test, & $property) {
						$test->{$property = uniqid()};
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Property \'' . $property . '\' is undefined in class \'' . get_class($test) . '\'')
			;
		}

		public function testEnableCodeCoverage()
		{
			$this
				->assert('Code coverage must be enabled only if xdebug is available')
					->if($adapter = new atoum\test\adapter())
					->and($adapter->extension_loaded = function($extension) { return $extension == 'xdebug'; })
					->and($test = new emptyTest(null, null, $adapter))
					->then
						->boolean($test->codeCoverageIsEnabled())->isTrue()
						->object($test->enableCodeCoverage())->isIdenticalTo($test)
						->boolean($test->codeCoverageIsEnabled())->isTrue()
					->if($test->disableCodeCoverage())
					->then
						->boolean($test->codeCoverageIsEnabled())->isFalse()
						->object($test->enableCodeCoverage())->isIdenticalTo($test)
						->boolean($test->codeCoverageIsEnabled())->isTrue()
				->assert('Code coverage must not be enabled if xdebug is not available')
					->if($adapter->extension_loaded = function($extension) { return ($extension == 'xdebug' ? false : true); })
					->and($test = new emptyTest(null, null, $adapter))
					->then
						->boolean($test->codeCoverageIsEnabled())->isFalse()
						->object($test->enableCodeCoverage())->isIdenticalTo($test)
						->boolean($test->codeCoverageIsEnabled())->isFalse()
			;
		}

		public function testDisableCodeCoverage()
		{
			$adapter = new atoum\test\adapter();
			$adapter->extension_loaded = true;

			$test = new emptyTest(null, null, $adapter);

			$this->assert
				->boolean($test->codeCoverageIsEnabled())->isTrue()
				->object($test->disableCodeCoverage())->isIdenticalTo($test)
				->boolean($test->codeCoverageIsEnabled())->isFalse()
				->when(function() use ($test) { $test->enableCodeCoverage(); })
					->boolean($test->codeCoverageIsEnabled())->isTrue()
					->object($test->disableCodeCoverage())->isIdenticalTo($test)
					->boolean($test->codeCoverageIsEnabled())->isFalse()
			;
		}

		public function testSetSuperglobals()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->setSuperglobals($superglobals = new atoum\superglobals()))->isIdenticalTo($test)
				->object($test->getSuperglobals())->isIdenticalTo($superglobals);
			;
		}

		public function testGetMockGenerator()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->getMockGenerator())->isInstanceOf('mageekguy\atoum\mock\generator')
			;

			$test->setMockGenerator($mockGenerator = new mock\generator());

			$this->assert
				->object($test->getMockGenerator())->isIdenticalTo($mockGenerator)
			;
		}

		public function testSetMockGenerator()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->setMockGenerator($mockGenerator = new mock\generator()))->isIdenticalTo($test)
				->object($test->getMockGenerator())->isIdenticalTo($mockGenerator)
			;
		}

		public function testGetAsserterGenerator()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->getAsserterGenerator())->isInstanceOf('mageekguy\atoum\asserter\generator')
			;

			$test->setAsserterGenerator($asserterGenerator = new atoum\asserter\generator(new emptyTest(), new atoum\locale()));

			$this->assert
				->object($test->getAsserterGenerator())->isIdenticalTo($asserterGenerator)
			;
		}

		public function testSetAsserterGenerator()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->setAsserterGenerator($asserterGenerator = new atoum\asserter\generator()))->isIdenticalTo($test)
				->object($test->getAsserterGenerator())->isIdenticalTo($asserterGenerator)
				->object($asserterGenerator->getTest())->isIdenticalTo($test)
				->object($asserterGenerator->getLocale())->isIdenticalTo($test->getLocale())
			;
		}

		public function testGetTestsSubNamespace()
		{
			$test = new self();

			$this->assert
				->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
			;

			$test->setTestNamespace($testsSubNamespace = uniqid());

			$this->assert
				->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
			;
		}

		public function testSetTestsSubNamespace()
		{
			$test = new self();

			$this->assert
				->object($test->setTestNamespace($testsSubNamespace = uniqid()))->isIdenticalTo($test)
				->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
				->object($test->setTestNamespace('\\' . ($testsSubNamespace = uniqid())))->isIdenticalTo($test)
				->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
				->object($test->setTestNamespace('\\' . ($testsSubNamespace = uniqid()) . '\\'))->isIdenticalTo($test)
				->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
				->object($test->setTestNamespace(($testsSubNamespace = uniqid()) . '\\'))->isIdenticalTo($test)
				->string($test->getTestNamespace())->isEqualTo($testsSubNamespace)
				->object($test->setTestNamespace($testsSubNamespace = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($test)
				->string($test->getTestNamespace())->isEqualTo((string) $testsSubNamespace)
			;

			$this->assert
				->exception(function() use ($test) {
							$test->setTestNamespace('');
						}
					)
					->isInstanceOf('invalidArgumentException')
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Test namespace must not be empty')
			;
		}

		public function testGetAdapter()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			;
		}

		public function testSetAdapter()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->setAdapter($adapter = new atoum\test\adapter()))->isIdenticalTo($test)
				->object($test->getAdapter())->isIdenticalTo($adapter)
			;
		}

		public function testSetLocale()
		{
			$test = new emptyTest();

			$locale = new atoum\locale();

			$this->assert
				->object($test->getLocale())->isNotIdenticalTo($locale)
				->object($test->setLocale($locale))->isIdenticalTo($test)
				->object($test->getLocale())->isIdenticalTo($locale)
			;
		}

		public function testSetScore()
		{
			$test = new emptyTest();

			$score = new atoum\score();

			$this->assert
				->object($test->getScore())->isNotIdenticalTo($score)
				->object($test->setScore($score))->isIdenticalTo($test)
				->object($test->getScore())->isIdenticalTo($score)
			;
		}

		public function testSetBootstrapFile()
		{
			$this->assert
				->if($test = new emptyTest())
				->then
					->object($test->setBootstrapFile($path = uniqid()))->isIdenticalTo($test)
					->string($test->getBootstrapFile())->isEqualTo($path)
			;
		}

		public function testSetMaxChildrenNumber()
		{
			$test = new emptyTest();

			$this->assert
				->exception(function() use ($test) { $test->setMaxChildrenNumber(- rand(1, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Maximum number of children must be greater or equal to 1')
				->exception(function() use ($test) { $test->setMaxChildrenNumber(0); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Maximum number of children must be greater or equal to 1')
				->object($test->setMaxChildrenNumber($maxChildrenNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($test)
				->integer($test->getMaxChildrenNumber())->isEqualTo($maxChildrenNumber)
			;
		}

		public function testGetClass()
		{
			$test = new emptyTest();

			$this->assert
				->string($test->getClass())->isEqualTo(__NAMESPACE__ . '\emptyTest')
			;
		}

		public function testGetPath()
		{
			$test = new emptyTest();

			$this->assert
				->string($test->getPath())->isEqualTo(__FILE__)
			;
		}

		public function testGetCoverage()
		{
			$this->assert
				->if($test = new emptyTest())
				->then
					->object($test->getCoverage())->isIdenticalTo($test->getScore()->getCoverage())
			;
		}

		public function testIgnore()
		{
			$test = new emptyTest();

			$this->assert
				->boolean($test->isIgnored())->isTrue()
				->object($test->ignore(false))->isIdenticalTo($test)
				->boolean($test->isIgnored())->isFalse()
				->object($test->ignore(true))->isIdenticalTo($test)
				->boolean($test->isIgnored())->isTrue()
			;

			$test = new notEmptyTest();

			$this->assert
				->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
				->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
				->object($test->ignore(false))->isIdenticalTo($test)
				->boolean($test->methodIsIgnored('testMethod1'))->isFalse()
				->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
				->object($test->ignore(true))->isIdenticalTo($test)
				->boolean($test->methodIsIgnored('testMethod1'))->istrue()
				->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
			;
		}

		public function testGetCurrentMethod()
		{
			$test = new emptyTest();

			$this->assert
				->variable($test->getCurrentMethod())->isNull()
			;
		}

		public function testCount()
		{
			$this->assert
				->sizeOf(new emptyTest())->isEqualTo(0)
			;

			$test = new notEmptyTest();

			$this->assert
				->if
					->boolean($test->isIgnored())->isTrue()
				->then
					->sizeOf($test)->isEqualTo(0)
				->if($test->ignore(false))
				->then
					->boolean($test->methodIsIgnored('testMethod1'))->isFalse()
					->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
					->sizeOf($test)->isEqualTo(2)
				->if($test->ignoreMethod('testMethod1', true))
					->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
					->sizeOf($test)->isEqualTo(1)
				->if($test->ignoreMethod('testMethod2', true))
					->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
					->sizeOf($test)->isEqualTo(0)
			;
		}

		public function testGetTestMethods()
		{
			$test = new emptyTest();

			$this->assert
				->boolean($test->ignore(false)->isIgnored())->isFalse()
				->sizeOf($test)->isZero()
				->array($test->getTestMethods())->isEmpty()
			;

			$test = new notEmptyTest();

			$this->assert
				->boolean($test->isIgnored())->isTrue()
				->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
				->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
				->sizeOf($test)->isEqualTo(0)
				->array($test->getTestMethods())->isEmpty()
				->boolean($test->ignore(false)->isIgnored())->isFalse()
				->boolean($test->methodIsIgnored('testMethod1'))->isFalse()
				->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
				->sizeOf($test)->isEqualTo(2)
				->array($test->getTestMethods())->isEqualTo(array('testMethod1', 'testMethod2'))
				->array($test->getTestMethods(array('test')))->isEqualTo(array('testMethod1', 'testMethod2'))
				->array($test->getTestMethods(array('method')))->isEqualTo(array('testMethod1', 'testMethod2'))
				->array($test->getTestMethods(array('two')))->isEqualTo(array('testMethod2'))
				->array($test->getTestMethods(array(uniqid())))->isEmpty()
				->array($test->getTestMethods(array('test', 'method')))->isEqualTo(array('testMethod1', 'testMethod2'))
				->array($test->getTestMethods(array('test', 'method', uniqid())))->isEqualTo(array('testMethod1', 'testMethod2'))
				->array($test->getTestMethods(array('test', 'method', 'two', uniqid())))->isEqualTo(array('testMethod1', 'testMethod2'))
			;
		}

		public function testGetPhpPath()
		{
			$test = new emptyTest();

			$this->assert
				->variable($test->getPhpPath())->isNull()
			;

			$test->setPhpPath($phpPath = uniqid());

			$this->assert
				->string($test->getPhpPath())->isEqualTo($phpPath)
			;
		}

		public function testSetPhpPath()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->setPhpPath($phpPath = uniqid()))->isIdenticalTo($test)
				->string($test->getPhpPath())->isIdenticalTo($phpPath)
			;

			$this->assert
				->object($test->setPhpPath($phpPath = rand(1, PHP_INT_MAX)))->isIdenticalTo($test)
				->string($test->getPhpPath())->isIdenticalTo((string) $phpPath)
			;
		}

		public function testMethodIsIgnored()
		{
			$test = new emptyTest();

			$this->assert
				->exception(function() use ($test, & $method) { $test->methodIsIgnored($method = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Test method ' . get_class($test) . '::' . $method . '() is unknown')
			;
		}

		public function testSetTags()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->setTags($tags = array(uniqid(), uniqid())))->isIdenticalTo($test)
				->array($test->getTags())->isEqualTo($tags)
			;
		}

		public function testSetMethodTags()
		{
			$test = new notEmptyTest();

			$this->assert
				->object($test->setMethodTags('testMethod1', $tags = array(uniqid(), uniqid())))->isIdenticalTo($test)
				->array($test->getMethodTags('testMethod1'))->isEqualTo($tags)
				->exception(function() use ($test, & $method) { $test->setMethodTags($method = uniqid(), array()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Test method ' . get_class($test) . '::' . $method . '() is unknown')
			;
		}

		public function testGetMethodTags()
		{
			$test = new notemptyTest();

			$this->assert
				->array($test->getMethodTags('testMethod1'))->isEqualTo(array('test', 'method', 'one'))
				->exception(function() use ($test, & $method) { $test->getMethodTags($method = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Test method ' . get_class($test) . '::' . $method . '() is unknown')
			;
		}

		public function testRun()
		{
			$this
				->mock('test', 'mock')
				->mockTestedClass('mock\tests\units')
			;

			$test = new \mock\tests\units\test();

			$this->assert
				->object($test->run())->isIdenticalTo($test)
				->mock($test)
					->call('callObservers')
						->withArguments(\mageekguy\atoum\test::runStart)->once()
						->withArguments(\mageekguy\atoum\test::runStop)->once()
						->withArguments(\mageekguy\atoum\test::beforeSetUp)->never()
						->withArguments(\mageekguy\atoum\test::afterSetUp)->never()
						->withArguments(\mageekguy\atoum\test::beforeTestMethod)->never()
						->withArguments(\mageekguy\atoum\test::afterTestMethod)->never()
			;
		}

		public function testSetTestedClassName()
		{
			$test = new foo();

			$this->assert
				->string($test->getTestedClassName())->isEqualTo('mageekguy\atoum\test')
				->exception(function() use ($test) { $test->setTestedClassName(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Tested class name is already defined')
			;

			$test = new self();

			$this->assert
				->object($test->setTestedClassName($class = uniqid()))->isIdenticalTo($test)
				->string($test->getTestedClassName())->isEqualTo($class)
				->exception(function() use ($test) { $test->setTestedClassName(uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Tested class name is already defined')
			;
		}

		public function testMock()
		{
			$test = new emptyTest();

			$this->assert
				->object($test->mock(__CLASS__))->isIdenticalTo($test)
				->class('mock\\' . __CLASS__)->isSubClassOf(__CLASS__)
				->object($test->mock(__CLASS__, 'foo'))->isIdenticalTo($test)
				->class('foo\test')->isSubClassOf(__CLASS__)
				->object($test->mock(__CLASS__, 'foo\bar'))->isIdenticalTo($test)
				->class('foo\bar\test')->isSubClassOf(__CLASS__)
				->object($test->mock(__CLASS__, 'foo', 'bar'))->isIdenticalTo($test)
				->class('foo\bar')->isSubClassOf(__CLASS__)
			;
		}

		public function testMockTestedClass()
		{
			$test = new emptyTest();

			$testedClassName = $test->getTestedClassName();

			$this->assert
				->object($test->mockTestedClass())->isIdenticalTo($test)
				->class('mock\\' . $testedClassName)->isSubClassOf($testedClassName)
				->object($test->mockTestedClass('foo'))->isIdenticalTo($test)
				->class('foo\emptyTest')->isSubClassOf($testedClassName)
				->object($test->mockTestedClass('foo\bar'))->isIdenticalTo($test)
				->class('foo\bar\emptyTest')->isSubClassOf($testedClassName)
				->object($test->mockTestedClass('foo', 'bar'))->isIdenticalTo($test)
				->class('foo\bar')->isSubClassOf($testedClassName)
			;
		}

		public function testGetTaggedTestMethods()
		{
			$test = new emptyTest();

			$this->assert
				->array($test->getTaggedTestMethods(array()))->isEmpty()
				->array($test->getTaggedTestMethods(array(uniqid())))->isEmpty()
				->array($test->getTaggedTestMethods(array(uniqid(), uniqid())))->isEmpty()
			;

			$test = new notEmptyTest();

			$this->assert
				->if
					->boolean($test->isIgnored())->isTrue()
				->then
					->array($test->getTaggedTestMethods(array()))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod1', uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod1', uniqid(), 'testMethod2')))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2')))->isEmpty()
				->if($test->ignore(false))
				->then
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod1', uniqid())))->isEqualTo(array('testMethod1'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod2', uniqid())))->isEqualTo(array('testMethod2'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2')))->isEqualTo(array('Testmethod1', 'Testmethod2'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2'), array('one')))->isEqualTo(array('Testmethod1'))
				->if($test->ignoreMethod('testMethod1', true))
				->then
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod1', uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), 'testMethod2', uniqid())))->isEqualTo(array('testMethod2'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2')))->isEqualTo(array('Testmethod2'))
					->array($test->getTaggedTestMethods(array(uniqid(), 'Testmethod1', uniqid(), 'Testmethod2'), array('one')))->isEmpty()
			;
		}

		public function testSetDataProvider()
		{
			$test = new emptyTest();

			$this->assert
				->exception(function() use ($test, & $method) { $test->setDataProvider($method = uniqid(), uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Test method ' . get_class($test) . '::' . $method . '() is unknown')
			;

			$test = new notEmptyTest();

			$this->assert
				->exception(function() use ($test, & $dataProvider) { $test->setDataProvider('testMethod1', $dataProvider = uniqid()); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Data provider ' . get_class($test) . '::' . $dataProvider . '() is unknown')
				->object($test->setDataProvider('testMethod1', 'aDataProvider'))->isIdenticalTo($test)
				->array($test->getDataProviders())->isEqualTo(array('testMethod1' => 'aDataProvider'))
			;
		}
	}
}

?>
