<?php

namespace mageekguy\atoum
{
	class emptyTest {}
	class notEmptyTest {}

	class withStatic
	{
		static public function staticMethod($return)
		{
			return $return;
		}

		static public function someOtherStaticMethod($return1, $return2, $return3)
		{
			return array($return1, $return2, $return3);
		}
	}

	class dummy
	{
		public function __construct($firstArgument, $secondArgument) {}
	}
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
	 * @ignore on
	 * @tags empty fake dummy
	 * @maxChildrenNumber 666
	 */
	class emptyTest extends atoum\test {}

	/**
	 * @ignore on
	 */
	class notEmptyTest extends atoum\test
	{
		/**
		@tags test method one method
		*/
		public function testMethod1() {}

		/**
		 * @extensions mbstring socket
		 * @ignore off
		 * @tags test method two
		*/
		public function testMethod2() {}

		public function aDataProvider()
		{
		}
	}

	/**
	 * @ignore on
	 * @tags first
	 */
	class inheritedTagsTest extends atoum\test
	{
		/**
		 * @tags second third
		 */
		public function testMethod1() {}

		/**
		 * @tags first second third
		 */
		public function testMethod2() {}
	}

	/**
	 * @ignore on
	 */
	class dataProviderTest extends atoum\test
	{
		public function testMethod1(\stdClass $a) {}

		public function testMethod2(\splFileInfo $a) {}

		public function testMethod3($a) {}
	}

	class foo extends atoum\test
	{
		public function __construct()
		{
			$this->setTestedClassName('mageekguy\atoum\test');

			parent::__construct();
		}
	}

	/**
	 * @ignore on
	 */
	class withStatic extends atoum\test
	{
		public function __construct()
		{
			$this->setTestedClassName('mageekguy\atoum\withStatic');

			parent::__construct();
		}
	}

	class test extends atoum\test
	{
		public function testClassConstants()
		{
			$this
				->string(atoum\test::testMethodPrefix)->isEqualTo('test')
				->string(atoum\test::runStart)->isEqualTo('testRunStart')
				->string(atoum\test::beforeSetUp)->isEqualTo('beforeTestSetUp')
				->string(atoum\test::afterSetUp)->isEqualTo('afterTestSetUp')
				->string(atoum\test::beforeTestMethod)->isEqualTo('beforeTestMethod')
				->string(atoum\test::fail)->isEqualTo('testAssertionFail')
				->string(atoum\test::error)->isEqualTo('testError')
				->string(atoum\test::uncompleted)->isEqualTo('testUncompleted')
				->string(atoum\test::skipped)->isEqualTo('testSkipped')
				->string(atoum\test::exception)->isEqualTo('testException')
				->string(atoum\test::success)->isEqualTo('testAssertionSuccess')
				->string(atoum\test::afterTestMethod)->isEqualTo('afterTestMethod')
				->string(atoum\test::beforeTearDown)->isEqualTo('beforeTestTearDown')
				->string(atoum\test::afterTearDown)->isEqualTo('afterTestTearDown')
				->string(atoum\test::runStop)->isEqualTo('testRunStop')
				->string(atoum\test::defaultNamespace)->isEqualTo('#(?:^|\\\\)tests?\\\\units?\\\\#i')
				->string(atoum\test::defaultMethodPrefix)->isEqualTo('#^(?:test|_*[^_]+_should_)#i')
			;
		}

		public function test__construct()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getScore())->isInstanceOf('mageekguy\atoum\score')
					->object($test->getLocale())->isEqualTo(new atoum\locale())
					->object($test->getAdapter())->isEqualTo(new atoum\adapter())
					->object($test->getPhpFunctionMocker())->isInstanceOf('mageekguy\atoum\php\mocker\funktion')
					->object($test->getPhpConstantMocker())->isInstanceOf('mageekguy\atoum\php\mocker\constant')
					->object($test->getFactoryBuilder())->isInstanceOf('mageekguy\atoum\factory\builder\closure')
					->boolean($test->isIgnored())->isTrue()
					->boolean($test->debugModeIsEnabled())->isFalse()
					->array($test->getAllTags())->isEqualTo($tags = array('empty', 'fake', 'dummy'))
					->array($test->getTags())->isEqualTo($tags)
					->array($test->getMethodTags())->isEmpty()
					->array($test->getDataProviders())->isEmpty()
					->integer($test->getMaxChildrenNumber())->isEqualTo(666)
					->boolean($test->codeCoverageIsEnabled())->isEqualTo(extension_loaded('xdebug'))
					->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
					->integer($test->getMaxChildrenNumber())->isEqualTo(666)
					->variable($test->getBootstrapFile())->isNull()
					->array($test->getClassPhpVersions())->isEmpty()
					->array($test->getMandatoryClassExtensions())->isEmpty()
					->array($test->getMandatoryMethodExtensions())->isEmpty()
					->variable($test->getXdebugConfig())->isNull()
			;
		}

		public function test__toString()
		{
			$this->castToString($this)->isEqualTo(__CLASS__);
		}

		public function test__get()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->assert)->isInstanceOf('mageekguy\atoum\test')
					->object($test->define)->isInstanceOf('mageekguy\atoum\test\assertion\aliaser')
					->object($test->mockGenerator)->isInstanceOf('mageekguy\atoum\mock\generator')
				->if($test->setMockGenerator($mockGenerator = new atoum\test\mock\generator($this)))
				->then
					->object($test->mockGenerator)->isIdenticalTo($mockGenerator)
				->if($test->setAsserterGenerator($asserterGenerator = new atoum\test\asserter\generator(new emptyTest())))
				->then
					->object($test->assert)->isIdenticalTo($test)
					->variable($test->exception)->isNull()
					->exception(function() use ($test, & $property) { $test->{$property = uniqid()}; })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Asserter \'' . $property . '\' does not exist')
					->exception($test->exception)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Asserter \'' . $property . '\' does not exist')
			;
		}

		public function test__set()
		{
			$this
				->given(
					$test = new emptyTest(),
					$test->setAssertionManager($assertionManager = new \mock\mageekguy\atoum\test\assertion\manager())
				)

				->if($test->{$event = uniqid()} = $handler = function() {})
				->then
					->mock($assertionManager)->call('setHandler')->withArguments($event, $handler)->once()
			;
		}

		public function testEnableDebugMode()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->enableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isTrue()
					->object($test->enableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isTrue()
			;
		}

		public function testDisableDebugMode()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->disableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isFalse()
					->object($test->disableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isFalse()
				->if($test->enableDebugMode())
				->then
					->object($test->disableDebugMode())->isIdenticalTo($test)
					->boolean($test->debugModeIsEnabled())->isFalse()
			;
		}

		public function testEnableCodeCoverage()
		{
			$this
				->assert('Code coverage must be enabled only if xdebug is available')
					->if($adapter = new atoum\test\adapter())
					->and($adapter->extension_loaded = function($extension) { return $extension == 'xdebug'; })
					->and($test = new emptyTest($adapter))
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
					->if($adapter->extension_loaded = function($extension) { return $extension != 'xdebug'; })
					->and($test = new emptyTest($adapter))
					->then
						->boolean($test->codeCoverageIsEnabled())->isFalse()
						->object($test->enableCodeCoverage())->isIdenticalTo($test)
						->boolean($test->codeCoverageIsEnabled())->isFalse()
			;
		}

		public function testDisableCodeCoverage()
		{
			$this
				->if($adapter = new atoum\test\adapter())
				->and($adapter->extension_loaded = true)
				->and($test = new emptyTest($adapter))
				->then
					->boolean($test->codeCoverageIsEnabled())->isTrue()
					->object($test->disableCodeCoverage())->isIdenticalTo($test)
					->boolean($test->codeCoverageIsEnabled())->isFalse()
				->if($test->enableCodeCoverage())
				->then
					->boolean($test->codeCoverageIsEnabled())->isTrue()
					->object($test->disableCodeCoverage())->isIdenticalTo($test)
					->boolean($test->codeCoverageIsEnabled())->isFalse()
			;
		}

		public function testGetMockGenerator()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getMockGenerator())->isInstanceOf('mageekguy\atoum\mock\generator')
				->if($test->setMockGenerator($mockGenerator = new atoum\test\mock\generator($this)))
				->then
					->object($test->getMockGenerator())->isIdenticalTo($mockGenerator)
					->object($mockGenerator->getTest())->isIdenticalTo($test)
			;
		}

		public function testSetMockGenerator()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setMockGenerator($mockGenerator = new atoum\test\mock\generator($this)))->isIdenticalTo($test)
					->object($test->getMockGenerator())->isIdenticalTo($mockGenerator)
					->object($mockGenerator->getTest())->isIdenticalTo($test)
			;
		}

		public function testSetMockAutoloader()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setMockAutoloader($mockAutoloader = new atoum\autoloader\mock()))->isIdenticalTo($test)
					->object($test->getMockAutoloader())->isIdenticalTo($mockAutoloader)
			;
		}

		public function testGetAsserterGenerator()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getAsserterGenerator())->isInstanceOf('mageekguy\atoum\test\asserter\generator')
				->if($test->setAsserterGenerator($asserterGenerator = new atoum\test\asserter\generator($this)))
				->then
					->object($test->getAsserterGenerator())->isIdenticalTo($asserterGenerator)
					->object($asserterGenerator->getTest())->isIdenticalTo($test)
			;
		}

		public function testSetAsserterGenerator()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setAsserterGenerator($asserterGenerator = new atoum\test\asserter\generator($test)))->isIdenticalTo($test)
					->object($test->getAsserterGenerator())->isIdenticalTo($asserterGenerator)
					->object($asserterGenerator->getTest())->isIdenticalTo($test)
					->object($asserterGenerator->getLocale())->isIdenticalTo($test->getLocale())
			;
		}

		public function testGetFactoryBuilder()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getFactoryBuilder())->isInstanceOf('mageekguy\atoum\factory\builder\closure')

				->if($test->setFactoryBuilder($factoryBuilder = new \mock\atoum\factory\builder()))
				->then
					->object($test->getFactoryBuilder())->isIdenticalTo($factoryBuilder)
			;
		}

		public function testSetFactoryBuilder()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setFactoryBuilder($factoryBuilder = new \mock\atoum\factory\builder()))->isIdenticalTo($test)
					->object($test->getFactoryBuilder())->isIdenticalTo($factoryBuilder)

					->object($test->setFactoryBuilder())->isIdenticalTo($test)
					->object($test->getFactoryBuilder())
						->isEqualTo(new atoum\Factory\builder\closure())
						->isNotIdenticalTo($factoryBuilder)
			;
		}

		public function testSetPhpFunktionMocker()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setPhpFunctionMocker($phpFunctionMocker = new atoum\php\mocker\funktion()))->isIdenticalTo($test)
					->object($test->getPhpFunctionMocker())->isIdenticalTo($phpFunctionMocker)
					->object($test->setPhpFunctionMocker())->isIdenticalTo($test)
					->object($test->getPhpFunctionMocker())
						->isNotIdenticalTo($phpFunctionMocker)
						->isInstanceOf('mageekguy\atoum\php\mocker\funktion')
			;
		}

		public function testSetTestNamespace()
		{
			$this
				->if($test = new self())
				->then
					->object($test->setTestNamespace($testNamespace = uniqid('_')))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
					->object($test->setTestNamespace('\\' . $testNamespace))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
					->object($test->setTestNamespace($testNamespace . '\\'))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
					->object($test->setTestNamespace('\\' . $testNamespace . '\\'))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)

					->object($test->setTestNamespace($testNamespace = uniqid('_') . '\\' . $testNamespace))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
					->object($test->setTestNamespace('\\' . $testNamespace))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
					->object($test->setTestNamespace($testNamespace . '\\'))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
					->object($test->setTestNamespace('\\' . $testNamespace . '\\'))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)

					->object($test->setTestNamespace($testNamespace = '_' . rand(0, PHP_INT_MAX)))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo((string) $testNamespace)
					->object($test->setTestNamespace('\\' . $testNamespace))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
					->object($test->setTestNamespace($testNamespace . '\\'))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
					->object($test->setTestNamespace('\\' . $testNamespace . '\\'))->isIdenticalTo($test)
					->string($test->getTestNamespace())->isEqualTo($testNamespace)


					->exception(function() use ($test) {
								$test->setTestNamespace('');
							}
						)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test namespace must not be empty')
					->exception(function() use ($test) {
								$test->setTestNamespace('0');
							}
						)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test namespace must be a valid regex or identifier')
					->exception(function() use ($test) {
								$test->setTestNamespace(uniqid('_') . '\\\\' . uniqid('_'));
							}
						)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test namespace must be a valid regex or identifier')
			;
		}

		public function testGetTestNamespace()
		{
			$this
				->if($test = new self())
				->then
					->string($test->getTestNamespace())->isEqualTo(atoum\test::defaultNamespace)
				->if($test->setTestNamespace($testNamespace = uniqid('_')))
				->then
					->string($test->getTestNamespace())->isEqualTo($testNamespace)
			;
		}

		public function testSetTestMethodPrefix()
		{
			$this
				->if($test = new self())
				->then
					->object($test->setTestMethodPrefix($testMethodPrefix = uniqid('_')))->isIdenticalTo($test)
					->string($test->getTestMethodPrefix())->isEqualTo($testMethodPrefix)
					->object($test->setTestMethodPrefix($testMethodPrefix = '/^test/i'))->isIdenticalTo($test)
					->string($test->getTestMethodPrefix())->isEqualTo($testMethodPrefix)
					->object($test->setTestMethodPrefix($testMethodPrefix = ('_'.rand(0, PHP_INT_MAX))))->isIdenticalTo($test)
					->string($test->getTestMethodPrefix())->isEqualTo((string) $testMethodPrefix)
					->object($test->setTestMethodPrefix($testMethodPrefix = "_0"))->isIdenticalTo($test)
					->string($test->getTestMethodPrefix())->isEqualTo((string) $testMethodPrefix)
					->exception(function() use ($test) {
								$test->setTestMethodPrefix('');
							}
						)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method prefix must not be empty')
					->exception(function() use ($test) {
								$test->setTestMethodPrefix('0');
							}
						)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method prefix must a valid regex or identifier')
					->exception(function() use ($test) {
								$test->setTestMethodPrefix('/:(/');
							}
						)
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method prefix must a valid regex or identifier')
			;
		}

		public function testGetTestMethodPrefix()
		{
			$this
				->if($test = new self())
				->then
					->string($test->getTestMethodPrefix())->isEqualTo(atoum\test::defaultMethodPrefix)
				->if($test->setTestMethodPrefix($testMethodPrefix = uniqid('_')))
				->then
					->string($test->getTestMethodPrefix())->isEqualTo($testMethodPrefix)
			;
		}

		public function testGetTestedClassName()
		{
			$mockClass = '\mock\\' . __CLASS__;

			$this
				->if($test = new $mockClass())
				->and($test->getMockController()->getClass = $testClass = 'foo')
				->then
					->exception(function() use ($test) { $test->getTestedClassName(); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Test class \'' . $testClass . '\' is not in a namespace which match pattern \'' . $test->getTestNamespace() . '\'')
				->if($test->getMockController()->getClass = 'tests\units\foo')
				->then
					->string($test->getTestedClassName())->isEqualTo('foo')
			;
		}

		public function testGetTestedClassPath()
		{
			$this
				->if($testedClass = new \reflectionClass($this->getTestedClassName()))
				->then
					->string($this->getTestedClassPath())->isEqualTo($testedClass->getFilename())
			;
		}

		public function testGetAdapter()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			;
		}

		public function testSetAdapter()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setAdapter($adapter = new atoum\test\adapter()))->isIdenticalTo($test)
					->object($test->getAdapter())->isIdenticalTo($adapter)
			;
		}

		public function testSetLocale()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setLocale($locale = new atoum\locale()))->isIdenticalTo($test)
					->object($test->getLocale())->isIdenticalTo($locale)
			;
		}

		public function testSetScore()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setScore($score = new atoum\test\score()))->isIdenticalTo($test)
					->object($test->getScore())->isIdenticalTo($score)
			;
		}

		public function testSetBootstrapFile()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setBootstrapFile($path = uniqid()))->isIdenticalTo($test)
					->string($test->getBootstrapFile())->isEqualTo($path)
			;
		}

		public function testSetMaxChildrenNumber()
		{
			$this
				->if($test = new emptyTest())
				->then
					->exception(function() use ($test) { $test->setMaxChildrenNumber(- rand(1, PHP_INT_MAX)); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Maximum number of children must be greater or equal to 1')
					->exception(function() use ($test) { $test->setMaxChildrenNumber(0); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Maximum number of children must be greater or equal to 1')
					->object($test->setMaxChildrenNumber($maxChildrenNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($test)
					->integer($test->getMaxChildrenNumber())->isEqualTo($maxChildrenNumber)
					->object($test->setMaxChildrenNumber((string) $maxChildrenNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($test)
					->integer($test->getMaxChildrenNumber())->isEqualTo($maxChildrenNumber)
			;
		}

		public function testGetClass()
		{
			$this
				->if($test = new emptyTest())
				->then
					->string($test->getClass())->isEqualTo(__NAMESPACE__ . '\emptyTest')
			;
		}

		public function testGetPath()
		{
			$this
				->if($test = new emptyTest())
				->then
					->string($test->getPath())->isEqualTo(__FILE__)
			;
		}

		public function testGetCoverage()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getCoverage())->isIdenticalTo($test->getScore()->getCoverage())
			;
		}

		public function testIsIgnored()
		{
			$this
				->if($test = new emptyTest())
				->then
					->boolean($test->isIgnored())->isTrue()
					->object($test->ignore(false))->isIdenticalTo($test)
					->boolean($test->isIgnored())->isTrue()
					->object($test->ignore(true))->isIdenticalTo($test)
					->boolean($test->isIgnored())->isTrue()
				->if($test = new notEmptyTest())
				->then
					->boolean($test->isIgnored())->isTrue()
					->boolean($test->methodIsIgnored('testMethod1'))->isTrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
					->object($test->ignore(false))->isIdenticalTo($test)
					->boolean($test->isIgnored())->isFalse()
					->boolean($test->methodIsIgnored('testMethod1'))->isFalse()
					->boolean($test->methodIsIgnored('testMethod2'))->isFalse()
					->object($test->ignore(true))->isIdenticalTo($test)
					->boolean($test->isIgnored())->isTrue()
					->boolean($test->methodIsIgnored('testMethod1'))->istrue()
					->boolean($test->methodIsIgnored('testMethod2'))->isTrue()
			;
		}

		public function testGetCurrentMethod()
		{
			$this
				->if($test = new emptyTest())
				->then
					->variable($test->getCurrentMethod())->isNull()
			;
		}

		public function testCount()
		{
			$this
				->sizeOf(new emptyTest())->isEqualTo(0)
				->if($test = new notEmptyTest())
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
			$this
				->if($test = new emptyTest())
				->then
					->boolean($test->ignore(false)->isIgnored())->isTrue()
					->sizeOf($test)->isZero()
					->array($test->getTestMethods())->isEmpty()
				->if($test = new notEmptyTest())
				->then
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
					->array($test->getTestMethods(array('method')))->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('test')))->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('two')))->isEqualTo(array('testMethod2'))
					->array($test->getTestMethods(array(uniqid())))->isEmpty()
					->array($test->getTestMethods(array('test', 'method')))->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('test', 'method', uniqid())))->isEqualTo(array('testMethod1', 'testMethod2'))
					->array($test->getTestMethods(array('test', 'method', 'two', uniqid())))->isEqualTo(array('testMethod1', 'testMethod2'))
			;
		}

		public function testGetPhpPath()
		{
			$this
				->if($test = new emptyTest())
				->then
					->variable($test->getPhpPath())->isNull()
				->if($test->setPhpPath($phpPath = uniqid()))
				->then
					->string($test->getPhpPath())->isEqualTo($phpPath)
			;
		}

		public function testSetPhpPath()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setPhpPath($phpPath = uniqid()))->isIdenticalTo($test)
					->string($test->getPhpPath())->isIdenticalTo($phpPath)
					->object($test->setPhpPath($phpPath = rand(1, PHP_INT_MAX)))->isIdenticalTo($test)
					->string($test->getPhpPath())->isIdenticalTo((string) $phpPath)
			;
		}

		public function testMethodIsIgnored()
		{
			$this
				->if($test = new emptyTest())
				->then
					->exception(function() use ($test, & $method) { $test->methodIsIgnored($method = uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
			;
		}

		public function testSetTags()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->setTags($tags = array(uniqid(), uniqid())))->isIdenticalTo($test)
					->array($test->getTags())->isEqualTo($tags)
			;
		}

		public function testSetMethodTags()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->object($test->setMethodTags('testMethod1', $tags = array(uniqid(), uniqid())))->isIdenticalTo($test)
					->array($test->getMethodTags('testMethod1'))->isEqualTo($tags)
					->exception(function() use ($test, & $method) { $test->setMethodTags($method = uniqid(), array()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
			;
		}

		public function testGetMethodTags()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->array($test->getMethodTags('testMethod1'))->isEqualTo(array('test', 'method', 'one'))
					->exception(function() use ($test, & $method) { $test->getMethodTags($method = uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
				->if($test = new inheritedTagsTest())
				->then
					->array($test->getMethodTags())->isEqualTo(array('testMethod1' => array('first', 'second', 'third'), 'testMethod2' => array('first', 'second', 'third')))
					->array($test->getMethodTags('testMethod1'))->isEqualTo(array('first', 'second', 'third'))
					->array($test->getMethodTags('testMethod2'))->isEqualTo(array('first', 'second', 'third'))
				->if($test = new dataProviderTest())
				->then
					->array($test->getMethodTags())->isEqualTo(array('testMethod1' => array(), 'testMethod2' => array(), 'testMethod3' => array()))
					->array($test->getMethodTags('testMethod1'))->isEqualTo(array())
					->array($test->getMethodTags('testMethod2'))->isEqualTo(array())
					->array($test->getMethodTags('testMethod3'))->isEqualTo(array())
			;
		}

		public function testAddMandatoryClassExtension()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->object($test->addMandatoryClassExtension($extension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryClassExtensions())->isEqualTo(array($extension))
					->object($test->addMandatoryClassExtension($otherExtension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryClassExtensions())->isEqualTo(array($extension, $otherExtension))
			;
		}

		public function testGetMandatoryMethodExtensions()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEmpty()
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEqualTo(array('mbstring', 'socket'))
			;
		}

		public function testAddMandatoryMethodExtension()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->exception(function() use ($test, & $method) { $test->addMandatoryMethodExtension($method = uniqid(), uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
					->object($test->addMandatoryMethodExtension('testMethod1', $extension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryMethodExtensions())->isEqualTo(array('testMethod1' => array($extension), 'testMethod2' => array('mbstring', 'socket')))
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEqualTo(array($extension))
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEqualTo(array('mbstring', 'socket'))
					->object($test->addMandatoryMethodExtension('testMethod1', $otherExtension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryMethodExtensions())->isEqualTo(array('testMethod1' => array($extension, $otherExtension), 'testMethod2' => array('mbstring', 'socket')))
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEqualTo(array($extension, $otherExtension))
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEqualTo(array('mbstring', 'socket'))
					->object($test->addMandatoryMethodExtension('testMethod2', $anOtherExtension = uniqid()))->isIdenticalTo($test)
					->array($test->getMandatoryMethodExtensions())->isEqualTo(array('testMethod1' => array($extension, $otherExtension), 'testMethod2' => array('mbstring', 'socket', $anOtherExtension)))
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEqualTo(array($extension, $otherExtension))
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEqualTo(array('mbstring', 'socket', $anOtherExtension))
				->if($test->addMandatoryClassExtension($classExtension = uniqid()))
				->then
					->array($test->getMandatoryMethodExtensions())->isEqualTo(array('testMethod1' => array($classExtension, $extension, $otherExtension), 'testMethod2' => array($classExtension, 'mbstring', 'socket', $anOtherExtension)))
					->array($test->getMandatoryMethodExtensions('testMethod1'))->isEqualTo(array($classExtension, $extension, $otherExtension))
					->array($test->getMandatoryMethodExtensions('testMethod2'))->isEqualTo(array($classExtension, 'mbstring', 'socket', $anOtherExtension))
			;
		}

		public function testAddClassPhpVersion()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->object($test->addClassPhpVersion('5.3'))->isIdenticalTo($test)
					->array($test->getClassPhpVersions())->isEqualTo(array('5.3' => '>='))
					->object($test->addClassPhpVersion('5.4', '<='))->isIdenticalTo($test)
					->array($test->getClassPhpVersions())->isEqualTo(array('5.3' => '>=', '5.4' => '<='))
			;
		}

		public function testAddMethodPhpVersion()
		{
			$this
				->if($test = new notEmptyTest())
				->then
					->exception(function() use ($test, & $method) { $test->addMethodPhpVersion($method, '6.0'); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
					->object($test->addMethodPhpVersion('testMethod1', '5.3'))->isIdenticalTo($test)
					->array($test->getMethodPhpVersions())->isEqualTo(array('testMethod1' => array('5.3' => '>='), 'testMethod2' => array()))
					->array($test->getMethodPhpVersions('testMethod1'))->isEqualTo(array('5.3' => '>='))
					->array($test->getMethodPhpVersions('testMethod2'))->isEmpty()
					->object($test->addMethodPhpVersion('testMethod1', '5.4', '<='))->isIdenticalTo($test)
					->array($test->getMethodPhpVersions())->isEqualTo(array('testMethod1' => array('5.3' => '>=', '5.4' => '<='), 'testMethod2' => array()))
					->array($test->getMethodPhpVersions('testMethod1'))->isEqualTo(array('5.3' => '>=', '5.4' => '<='))
					->array($test->getMethodPhpVersions('testMethod2'))->isEmpty()
					->object($test->addMethodPhpVersion('testMethod2', '5.4', '>='))->isIdenticalTo($test)
					->array($test->getMethodPhpVersions())->isEqualTo(array('testMethod1' => array('5.3' => '>=', '5.4' => '<='), 'testMethod2' => array('5.4' => '>=')))
					->array($test->getMethodPhpVersions('testMethod1'))->isEqualTo(array('5.3' => '>=', '5.4' => '<='))
					->array($test->getMethodPhpVersions('testMethod2'))->isEqualTo(array('5.4' => '>='))
				->if($test->addClassPhpVersion('5.5'))
				->then
					->array($test->getMethodPhpVersions())->isEqualTo(array('testMethod1' => array('5.5' => '>=', '5.3' => '>=', '5.4' => '<='), 'testMethod2' => array('5.5' => '>=', '5.4' => '>=')))
					->array($test->getMethodPhpVersions('testMethod1'))->isEqualTo(array('5.5' => '>=', '5.3' => '>=', '5.4' => '<='))
					->array($test->getMethodPhpVersions('testMethod2'))->isEqualTo(array('5.5' => '>=', '5.4' => '>='))
			;
		}

		public function testRun()
		{
			$this
				->mockTestedClass('mock\tests\units')
				->if($test = new \mock\tests\units\test())
				->then
					->object($test->run())->isIdenticalTo($test)
					->mock($test)
						->call('callObservers')
							->withArguments(\mageekguy\atoum\test::runStart)->never()
							->withArguments(\mageekguy\atoum\test::runStop)->never()
							->withArguments(\mageekguy\atoum\test::beforeSetUp)->never()
							->withArguments(\mageekguy\atoum\test::afterSetUp)->never()
							->withArguments(\mageekguy\atoum\test::beforeTestMethod)->never()
							->withArguments(\mageekguy\atoum\test::afterTestMethod)->never()
			;
		}

		public function testSetTestedClassName()
		{
			$this
				->if($test = new foo())
				->then
					->string($test->getTestedClassName())->isEqualTo('mageekguy\atoum\test')
					->exception(function() use ($test) { $test->setTestedClassName(uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Tested class name is already defined')
				->if($test = new self())
				->then
					->object($test->setTestedClassName($class = uniqid()))->isIdenticalTo($test)
					->string($test->getTestedClassName())->isEqualTo($class)
					->exception(function() use ($test) { $test->setTestedClassName(uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Tested class name is already defined')
			;
		}

		public function testMockClass()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->mockClass(__CLASS__))->isIdenticalTo($test)
					->class('mock\\' . __CLASS__)->isSubClassOf(__CLASS__)
					->object($test->mockClass(__CLASS__, 'foo'))->isIdenticalTo($test)
					->class('foo\test')->isSubClassOf(__CLASS__)
					->object($test->mockClass(__CLASS__, 'foo\bar'))->isIdenticalTo($test)
					->class('foo\bar\test')->isSubClassOf(__CLASS__)
					->object($test->mockClass(__CLASS__, 'foo', 'bar'))->isIdenticalTo($test)
					->class('foo\bar')->isSubClassOf(__CLASS__)
			;
		}

		public function testMockTestedClass()
		{
			$this
				->if($test = new emptyTest())
				->and($testedClassName = $test->getTestedClassName())
				->then
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
			$this
				->if($test = new emptyTest())
				->then
					->array($test->getTaggedTestMethods(array()))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid())))->isEmpty()
					->array($test->getTaggedTestMethods(array(uniqid(), uniqid())))->isEmpty()
				->if($test = new notEmptyTest())
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
			$this
				->if($test = new emptyTest())
				->then
					->exception(function() use ($test, & $method) { $test->setDataProvider($method = uniqid(), uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Test method ' . get_class($test) . '::' . $method . '() does not exist')
				->if($test = new notEmptyTest())
				->then
					->exception(function() use ($test, & $dataProvider) { $test->setDataProvider('testMethod1', $dataProvider = uniqid()); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Data provider ' . get_class($test) . '::' . $dataProvider . '() is unknown')
					->object($test->setDataProvider('testMethod1', 'aDataProvider'))->isIdenticalTo($test)
					->array($test->getDataProviders())->isEqualTo(array('testMethod1' => 'aDataProvider'))
				->if($test = new dataProviderTest())
				->then
					->object($test->setDataProvider('testMethod2'))->isIdenticalTo($test)
					->array($providers = $test->getDataProviders())
						->object['testMethod2']->isInstanceOf('mageekguy\atoum\test\data\provider\aggregator')
					->exception(function() use ($providers) { $providers['testMethod2'](); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Could not instanciate a mock from ' . $test->getMockGenerator()->getDefaultNamespace() . '\\SplFileInfo because SplFileInfo::__construct() has at least one mandatory argument')
				->if($test->getMockGenerator()->allIsInterface())
				->then
					->exception(function() use ($providers) { $providers['testMethod2'](); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Could not instanciate a mock from ' . $test->getMockGenerator()->getDefaultNamespace() . '\\SplFileInfo because SplFileInfo::__construct() has at least one mandatory argument')
				->if($test->getMockGenerator()->setDefaultNamespace('testMocks'))
				->then
					->array($providers['testMethod2']())->isEqualTo(array(array(new \testMocks\splFileInfo())))
				->if($test = new dataProviderTest())
				->then
					->exception(function() use ($test, & $dataProvider) { $test->setDataProvider('testMethod3'); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Could not generate a data provider for ' . get_class($test) . '::testMethod3() because it has at least one argument which is not type-hinted with a class or interface name')
					->object($test->setDataProvider('testMethod1'))->isIdenticalTo($test)
					->array($test->getDataProviders())
						->object['testMethod1']->isInstanceOf('mageekguy\atoum\test\data\provider\aggregator')
				->if($test = new dataProviderTest())
				->then
					->exception(function() use ($test, & $dataProvider) { $test->setDataProvider('testMethod3', function() {}); })
						->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
						->hasMessage('Cannot use a closure as a data provider for method ' . get_class($test) . '::testMethod3()')
			;
		}

		public function testCalling()
		{
			$this
				->if($test = new emptyTest())
				->and($mock = new \mock\foo())
				->and($test->calling($mock)->bar = $value = uniqid())
				->then
					->string($mock->bar())->isEqualTo($value)
				->and($test->ƒ($mock)->bar = $otherValue = uniqid())
				->then
					->string($mock->bar())->isEqualTo($otherValue)
			;
		}

		public function testResetMock()
		{
			$this
				->if($test = new emptyTest())
				->and($mockController = new \mock\mageekguy\atoum\mock\controller())
				->and($mockController->control($mock = new \mock\object()))
				->and($this->resetMock($mockController))
				->then
					->object($test->resetMock($mock))->isIdenticalTo($mock->getMockController())
					->mock($mockController)->call('resetCalls')->once()
			;
		}

		public function testResetFunction()
		{
			$this
				->if($test = new emptyTest())
				->and($this->function->md5 = uniqid())
				->then
					->object($test->resetFunction($this->function->md5))->isIdenticalTo($this->function->md5)
			;
		}

		public function testResetAdapter()
		{
			$this
				->if($test = new emptyTest())
				->and($adapter = new \mock\mageekguy\atoum\test\adapter())
				->and($this->resetMock($adapter))
				->then
					->object($test->resetAdapter($adapter))->isIdenticalTo($adapter)
					->mock($adapter)->call('resetCalls')->once()
			;
		}

		public function testErrorHandler()
		{
			$this
				->if($test = new emptyTest())
				->and($adapter = new atoum\test\adapter())
				->and($adapter->error_reporting = 0)
				->and($test->setAdapter($adapter))
				->then
					->boolean($test->errorHandler(rand(1, PHP_INT_MAX), uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($test->getScore()->getErrors())->isEmpty()
				->if($adapter->error_reporting = E_ALL)
				->then
					->boolean($test->errorHandler(E_NOTICE, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_NOTICE))->isNotNull()
					->boolean($test->errorHandler(E_WARNING, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_WARNING))->isNotNull()
					->boolean($test->errorHandler(E_USER_NOTICE, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_USER_NOTICE))->isNotNull()
					->boolean($test->errorHandler(E_USER_WARNING, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_USER_WARNING))->isNotNull()
					->boolean($test->errorHandler(E_DEPRECATED, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_DEPRECATED))->isNotNull()
					->boolean($test->errorHandler(E_RECOVERABLE_ERROR, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isFalse()
					->variable($test->getScore()->errorExists($errstr, E_RECOVERABLE_ERROR))->isNotNull()
				->if($adapter->error_reporting = E_ALL & ~E_DEPRECATED)
				->then
					->boolean($test->errorHandler(E_NOTICE, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_NOTICE))->isNotNull()
					->boolean($test->errorHandler(E_WARNING, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_WARNING))->isNotNull()
					->boolean($test->errorHandler(E_USER_NOTICE, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_USER_NOTICE))->isNotNull()
					->boolean($test->errorHandler(E_USER_WARNING, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_USER_WARNING))->isNotNull()
					->boolean($test->errorHandler(E_DEPRECATED, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_DEPRECATED))->isNull()
				->if($adapter->error_reporting = E_ALL & ~E_RECOVERABLE_ERROR)
				->then
					->boolean($test->errorHandler(E_RECOVERABLE_ERROR, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->variable($test->getScore()->errorExists($errstr, E_RECOVERABLE_ERROR))->isNull()
				->if($adapter->error_reporting = 32767)
				->and($factory = function($class) use (& $reflection, & $filename, & $classname) {
					$reflection = new \mock\ReflectionClass($class);
					$reflection->getMockController()->getFilename = $filename = 'filename';
					$reflection->getMockController()->getName = $classname = 'classname';

					return $reflection;
				})
				->and($score = new \mock\mageekguy\atoum\test\score())
				->and($test = new emptyTest(null, null, null, null, $factory))
				->and($test->setAdapter($adapter))
				->and($test->setScore($score))
				->then
					->boolean($test->errorHandler($errno = E_NOTICE, $errstr = 'errstr', $errfile = 'errfile', $errline = rand(1, PHP_INT_MAX)))->isTrue()
					->mock($score)
						->call('addError')->withArguments($errfile, $classname, $test->getCurrentMethod(), $errline, $errno, $errstr, $errfile, $errline, null, null, null)->once()
					->boolean($test->errorHandler($errno, $errstr, null, $errline = rand(1, PHP_INT_MAX)))->isTrue()
					->mock($score)
						->call('addError')->withArguments($filename, $classname, $test->getCurrentMethod(), $errline, $errno, $errstr, null, $errline, null, null, null)->once()
			;
		}

		public function testGetTestedClassNameFromTestClass()
		{
			$this
				->string(atoum\test::getTestedClassNameFromTestClass(__CLASS__))->isEqualTo('mageekguy\atoum\test')
				->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\tests\units\testedClass'))->isEqualTo('foo\bar\testedClass')
				->if(atoum\test::setNamespace('test\unit'))
				->then
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\test\unit\testedClass'))->isEqualTo('foo\bar\testedClass')
				->if(atoum\test::setNamespace('\test\unit\\'))
				->then
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\test\unit\testedClass'))->isEqualTo('foo\bar\testedClass')
				->if(atoum\test::setNamespace('test\unit\\'))
				->then
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\test\unit\testedClass'))->isEqualTo('foo\bar\testedClass')
				->if(atoum\test::setNamespace('\test\unit'))
				->then
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\test\unit\testedClass'))->isEqualTo('foo\bar\testedClass')
					->exception(function() { atoum\test::getTestedClassNameFromTestClass('foo\bar\aaa\bbb\testedClass'); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Test class \'foo\bar\aaa\bbb\testedClass\' is not in a namespace which contains \'' . atoum\test::getNamespace() . '\'')
				->if(atoum\test::setNamespace('#(?:^|\\\)xxxs?\\\yyys?\\\#i'))
				->then
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\xxx\yyy\testedClass'))->isEqualTo('foo\bar\testedClass')
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\xxxs\yyy\testedClass'))->isEqualTo('foo\bar\testedClass')
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\xxxs\yyys\testedClass'))->isEqualTo('foo\bar\testedClass')
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\xxx\yyys\testedClass'))->isEqualTo('foo\bar\testedClass')
					->exception(function() { atoum\test::getTestedClassNameFromTestClass('foo\bar\aaa\bbb\testedClass'); })
						->isInstanceOf('mageekguy\atoum\exceptions\runtime')
						->hasMessage('Test class \'foo\bar\aaa\bbb\testedClass\' is not in a namespace which match pattern \'' . atoum\test::getNamespace() . '\'')
					->string(atoum\test::getTestedClassNameFromTestClass('foo\bar\aaa\bbb\testedClass', '#(?:^|\\\)aaas?\\\bbbs?\\\#i'))->isEqualTo('foo\bar\testedClass')
			;
		}

		public function testAddExtension()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->addExtension($extension = new \mock\mageekguy\atoum\extension()))->isIdenticalTo($test)
					->array(iterator_to_array($test->getExtensions()))->isEqualTo(array($extension))
					->array($test->getObservers())->isEqualTo(array($extension))
					->mock($extension)
						->call('setTest')->withArguments($test)->once()
				->if($this->resetMock($extension))
				->then
					->object($test->addExtension($extension))->isIdenticalTo($test)
					->array(iterator_to_array($test->getExtensions()))->isEqualTo(array($extension))
					->array($test->getObservers())->isEqualTo(array($extension))
					->mock($extension)
						->call('setTest')->once();
			;
		}

		public function testRemoveExtension()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getExtensions())->isEqualTo(new \splObjectStorage())
					->array($test->getObservers())->isEmpty()
					->object($test->removeExtension(new \mock\mageekguy\atoum\extension()))->isIdenticalTo($test)
					->object($test->getExtensions())->isEqualTo(new \splObjectStorage())
					->array($test->getObservers())->isEmpty()
				->if($extension = new \mock\mageekguy\atoum\extension())
				->and($otherExtension = new \mock\mageekguy\atoum\extension())
				->and($test->addExtension($extension)->addExtension($otherExtension))
				->then
					->array(iterator_to_array($test->getExtensions()))->isEqualTo(array($extension, $otherExtension))
					->array($test->getObservers())->isEqualTo(array($extension, $otherExtension))
					->object($test->removeExtension(new \mock\mageekguy\atoum\extension()))->isIdenticalTo($test)
					->array(iterator_to_array($test->getExtensions()))->isEqualTo(array($extension, $otherExtension))
					->array($test->getObservers())->isEqualTo(array($extension, $otherExtension))
					->object($test->removeExtension($extension))->isIdenticalTo($test)
					->array(iterator_to_array($test->getExtensions()))->isEqualTo(array($otherExtension))
					->array($test->getObservers())->isEqualTo(array($otherExtension))
					->object($test->removeExtension($otherExtension))->isIdenticalTo($test)
					->object($test->getExtensions())->isEqualTo(new \splObjectStorage())
					->array($test->getObservers())->isEmpty()
			;
		}

		public function testRemoveExtensions()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($test->getExtensions())->isEqualTo(new \splObjectStorage())
					->array($test->getObservers())->isEmpty()
					->object($test->removeExtensions())->isIdenticalTo($test)
					->object($test->getExtensions())->isEqualTo(new \splObjectStorage())
					->array($test->getObservers())->isEmpty()
				->if($extension = new \mock\mageekguy\atoum\extension())
				->and($otherExtension = new \mock\mageekguy\atoum\extension())
				->and($test->addExtension($extension)->addExtension($otherExtension))
				->then
					->array(iterator_to_array($test->getExtensions()))->isEqualTo(array($extension, $otherExtension))
					->array($test->getObservers())->isEqualTo(array($extension, $otherExtension))
					->object($test->removeExtensions())->isIdenticalTo($test)
					->object($test->getExtensions())->isEqualTo(new \splObjectStorage())
					->array($test->getObservers())->isEmpty()
			;
		}

		public function testGetExtensionConfiguration()
		{
			$this
				->if(
					$test = new emptyTest(),
					$extension = new \mock\mageekguy\atoum\extension()
				)
				->then
					->variable($test->getExtensionConfiguration($extension))->isNull
				->if($test->addExtension($extension))
				->then
					->variable($test->getExtensionConfiguration($extension))->isNull
				->given($configuration = new \mock\mageekguy\atoum\extension\configuration())
				->if($test->addExtension($extension, $configuration))
				->then
					->object($test->getExtensionConfiguration($extension))->isIdenticalTo($configuration)
			;
		}

		public function testCallStaticOnTestedClass()
		{
			$this
				->if($test = new withStatic())
				->then
					->string($test->callStaticOnTestedClass('staticMethod', $return = uniqid()))
						->isEqualTo($return)

					->array($test->callStaticOnTestedClass(
						'someOtherStaticMethod',
						$return1 = uniqid(),
						$return2 = uniqid(),
						$return3 = uniqid()
					))
						->isEqualTo(array($return1, $return2, $return3))
			;
		}

		public function testNewMockInstance()
		{
			$this
				->if($test = new emptyTest())
				->then
					->object($mock = $test->newMockInstance('stdClass'))
						->isInstanceOf('mock\stdClass')
						->isInstanceOf('stdClass')
					->object($test->newMockInstance('stdClass'))
						->isInstanceOf('mock\stdClass')
						->isInstanceOf('stdClass')
						->isNotIdenticalTo($mock)
					->object($test->newMockInstance('stdClass', 'foobar'))
						->isInstanceOf('foobar\stdClass')
						->isInstanceOf('stdClass')
					->object($test->newMockInstance('stdClass', 'foo', 'bar'))
						->isInstanceOf('foo\bar')
						->isInstanceOf('stdClass')

				->given($arguments = array($firstArgument = uniqid(), $secondArgument = rand(0, PHP_INT_MAX)))
				->then
					->object($mock = $test->newMockInstance('mageekguy\atoum\dummy', null, null, $arguments))
						->isInstanceOf('mock\mageekguy\atoum\dummy')
						->isInstanceOf('mageekguy\atoum\dummy')
					->mock($mock)
						->call('__construct')->withArguments($firstArgument, $secondArgument)->once

				->given($arguments = array(uniqid(), rand(0, PHP_INT_MAX), $controller = new mock\controller()))
				->then
					->object($mock = $test->newMockInstance('mageekguy\atoum\dummy', null, null, $arguments))
						->isInstanceOf('mock\mageekguy\atoum\dummy')
						->isInstanceOf('mageekguy\atoum\dummy')
					->object($mock->getMockController())->isIdenticalTo($controller)

				->given(
					$arguments = array(uniqid(), rand(0, PHP_INT_MAX)),
					$controller = new mock\controller()
				)
				->then
					->object($mock = $test->newMockInstance('mageekguy\atoum\dummy', null, null, $arguments))
						->isInstanceOf('mock\mageekguy\atoum\dummy')
						->isInstanceOf('mageekguy\atoum\dummy')
					->object($mock->getMockController())->isIdenticalTo($controller)
			;
		}
	}
}
