<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\score,
	mageekguy\atoum\mock,
	mageekguy\atoum\reports\asynchronous\coveralls as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class coveralls extends atoum\test
{
	public function beforeTestMethod($method)
	{
		$this->extension('json')->isLoaded();
	}

	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\reports\asynchronous');
	}

	public function testClassConstants()
	{
		$this
			->string(testedClass::defaultServiceName)->isEqualTo('atoum')
			->string(testedClass::defaultEvent)->isEqualTo('manual')
			->string(testedClass::defaultCoverallsApiUrl)->isEqualTo('https://coveralls.io/api/v1/jobs')
			->string(testedClass::defaultCoverallsApiMethod)->isEqualTo('POST')
			->string(testedClass::defaultCoverallsApiParameter)->isEqualTo('json')
		;
	}

	public function test__construct()
	{
		$this
			->if($report = new testedClass($sourceDir = uniqid(), $token = uniqid()))
			->then
				->array($report->getFields(atoum\runner::runStart))->isEmpty()
				->object($report->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->array($report->getFields())->isEmpty()
				->object($report->getSourceDir())->isInstanceOf('\\mageekguy\\atoum\\fs\\path')
				->castToString($report->getSourceDir())->isEqualTo($sourceDir)
				->object($report->getBranchFinder())->isInstanceOf('\\Closure')
				->string($report->getServiceName())->isEqualTo('atoum')
				->variable($report->getServiceJobId())->isNull()
			->if($report = new testedClass($sourceDir, $token, $adapter = new atoum\test\adapter()))
			->then
				->adapter($report->getAdapter())->call('extension_loaded')->withArguments('json')->once()
			->if($adapter->extension_loaded = false)
			->then
				->exception(function() use ($adapter) {
								new testedClass(uniqid(), uniqid(), $adapter);
							}
						)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('JSON PHP extension is mandatory for coveralls report')
		;
	}

	public function testGetSetBranchFinder()
	{
		$this
			->if($report = new testedClass(uniqid(), uniqid()))
			->then
				->object($report->getBranchFinder())->isInstanceOf('\\Closure')
			->if($finder = function() {})
			->then
				->object($report->setBranchFinder($finder))->isIdenticalTo($report)
				->object($report->getBranchFinder())->isIdenticalTo($finder)
		;
	}

	public function testGetSetServiceName()
	{
		$this
			->if($report = new testedClass(uniqid(), uniqid()))
			->then
				->string($report->getServiceName())->isEqualTo('atoum')
			->if($service = uniqid())
			->then
				->object($report->setServiceName($service))->isIdenticalTo($report)
				->string($report->getServiceName())->isEqualTo($service)
		;
	}

	public function testGetSetServiceJobId()
	{
		$this
			->if($report = new testedClass(uniqid(), uniqid()))
			->then
				->variable($report->getServiceJobId())->isNull()
			->if($service = uniqid())
			->then
				->object($report->setServiceJobId($service))->isIdenticalTo($report)
				->string($report->getServiceJobId())->isEqualTo($service)
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($adapter->exec = function($command) {
				switch($command) {
					case 'git log -1 --pretty=format:\'{"id":"%H","author_name":"%aN","author_email":"%ae","committer_name":"%cN","committer_email":"%ce","message":"%s"}\'':
						return '{"id":"7282ea7620b45fcba0f9d3bfd484ab146aba2bd0","author_name":"mageekguy","author_email":"atoum@atoum.org","comitter_name":"mageekguy","comitter_email":"atoum@atoum.org"}';

					case 'git rev-parse --abbrev-ref HEAD':
						return 'master';

					default:
						return null;
				}
			})
			->and($report = new testedClass($sourceDir = uniqid(), $token = '51bb597d202b4', $adapter))
			->and($score = new \mock\mageekguy\atoum\score())
			->and($coverage = new \mock\mageekguy\atoum\score\coverage())
			->and($writer = new \mock\mageekguy\atoum\writers\http())
			->and($writer->getMockController()->writeAsynchronousReport = function() use ($writer) { return $writer; })
			->then
				->when(function() use ($report, $writer) {
						$report->addWriter($writer)->handleEvent(atoum\runner::runStop, new \mageekguy\atoum\runner());
					})
					->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
			->if($adapter->date = '2013-05-13 10:00:00 +0000')
			->and($adapter->file_get_contents = '<?php')
			->and($observable = new \mock\mageekguy\atoum\runner())
			->and($observable->getMockController()->getScore = $score)
			->and($score->getMockController()->getCoverage = $coverage)
			->and($coverage->getMockController()->getClasses = array())
			->and($filepath = join(
				DIRECTORY_SEPARATOR,
				array(
					__DIR__,
					'coveralls',
					'resources',
					'1.json'
				)
			))
			->and($report = new testedClass($sourceDir, $token, $adapter))
			->and($report->addWriter($writer))
			->then
				->object($report->handleEvent(atoum\runner::runStop, $observable))->isIdenticalTo($report)
				->castToString($report)->isEqualToContentsOfFile($filepath)
				->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
			->if($coverage->getMockController()->getClasses = array())
			->and($classController = new mock\controller())
			->and($classController->disableMethodChecking())
			->and($classController->__construct = function() {})
			->and($classController->getName = $className = 'bar')
			->and($classController->getFileName = $classFile = 'foo/bar.php')
			->and($classController->getTraits = array())
			->and($classController->getStartLine = 1)
			->and($classController->getEndLine = 12)
			->and($class = new \mock\reflectionClass(uniqid(), $classController))
			->and($methodController = new mock\controller())
			->and($methodController->__construct = function() {})
			->and($methodController->getName = $methodName = 'baz')
			->and($methodController->isAbstract = false)
			->and($methodController->getFileName = $classFile)
			->and($methodController->getDeclaringClass = $class)
			->and($methodController->getStartLine = 4)
			->and($methodController->getEndLine = 8)
			->and($classController->getMethods = array(new \mock\reflectionMethod($className, $methodName, $methodController)))
			->and($coverage->getMockController()->getClasses = array(
				$className => $classFile,
				'foo' => 'bar/foo.php'
			))
			->and($xdebugData = array(
				$classFile =>
				array(
					3 => 1,
					4 => 1,
					5 => -2,
					6 => 0,
					7 => -1,
					8 => 1,
					9 => 1
				)
			))
			->and($filepath = join(
				DIRECTORY_SEPARATOR,
				array(
					__DIR__,
					'coveralls',
					'resources',
					'2' . (defined('PHP_WINDOWS_VERSION_MAJOR') === true ? '-windows' : ''). '.json'
				)
			))
			->and($coverage->setReflectionClassFactory(function() use ($class) { return $class; }))
			->and($coverage->addXdebugDataForTest($this, $xdebugData))
			->then
				->object($report->handleEvent(atoum\runner::runStop, $observable))->isIdenticalTo($report)
				->castToString($report)->isEqualToContentsOfFile($filepath)
				->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->twice()
			->if($finder = function() use (& $branch) { return 'feature'; })
			->and($report->setBranchFinder($finder))
			->and($filepath = join(
				DIRECTORY_SEPARATOR,
				array(
					__DIR__,
					'coveralls',
					'resources',
					'3' . (defined('PHP_WINDOWS_VERSION_MAJOR') === true ? '-windows' : ''). '.json'
				)
			))
			->then
				->object($report->handleEvent(atoum\runner::runStop, $observable))->isIdenticalTo($report)
				->castToString($report)->isEqualToContentsOfFile($filepath)
				->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->thrice()
			->if($report->setBranchFinder(function() use(& $branch) { return $branch = uniqid(); }))
			->and($report->handleEvent(atoum\runner::runStop, $observable))
			->then
				->castToString($report)->contains('"branch":"' . $branch . '"')
			->if($report->setBranchFinder(function() {}))
			->and($report->handleEvent(atoum\runner::runStop, $observable))
			->then
				->castToString($report)->notContains('"branch":')
			->if($report->setBranchFinder(function() { return ''; }))
			->and($report->handleEvent(atoum\runner::runStop, $observable))
			->then
				->castToString($report)->notContains('"branch":')
		;
	}

	public function testAddDefaultWriter()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->extension_loaded = true)
			->and($adapter->file_get_contents = '')
			->and($adapter->stream_context_create = $context = uniqid())
			->and($report = new testedClass(uniqid(), uniqid(), $adapter))
			->and($writer = new \mock\mageekguy\atoum\writers\http())
			->then
				->object($report->addDefaultWriter($writer))->isIdenticalTo($report)
				->mock($writer)
					->call('setUrl')->withArguments(testedClass::defaultCoverallsApiUrl)->once()
					->call('setMethod')->withArguments(testedClass::defaultCoverallsApiMethod)->once()
					->call('setParameter')->withArguments(testedClass::defaultCoverallsApiParameter)->once()
					->call('addHeader')->withArguments('Content-Type', 'multipart/form-data')->once()
		;
	}
}
