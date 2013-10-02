<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\cli,
	mageekguy\atoum\mock\stream,
	mock\mageekguy\atoum as mock,
	mageekguy\atoum\scripts\runner as testedClass
;

require_once __DIR__ . '/../../runner.php';

class runner extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\script\configurable');
	}

	public function testClassConstants()
	{
		$this->assert
			->string(testedClass::defaultConfigFile)->isEqualTo('.atoum.php')
			->string(testedClass::defaultBootstrapFile)->isEqualTo('.bootstrap.atoum.php')
		;
	}

	public function test__construct()
	{
		$this
			->if($runner = new testedClass($name = uniqid()))
			->then
				->boolean($runner->hasDefaultArguments())->isFalse()
				->array($runner->getDefaultArguments())->isEmpty()
				->string($runner->getName())->isEqualTo($name)
				->object($runner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($runner->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($runner->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->object($runner->getRunner())->isInstanceOf('mageekguy\atoum\runner')
				->variable($runner->getScoreFile())->isNull()
				->array($runner->getReports())->isEmpty()
				->array($runner->getArguments())->isEmpty()
				->array($runner->getHelp())->isEqualTo(array(
						array(
							array('-h', '--help'),
							null,
							'Display this help'
						),
						array(
							array('-c', '--configurations'),
							'<file>...',
							'Use all configuration files <file>'
						),
						array(
							array('-v', '--version'),
							null,
							'Display version'
						),
						array(
							array('+verbose', '++verbose'),
							null,
							'Enable verbose mode'
						),
						array(
							array('--init'),
							null,
							'Create configuration and bootstrap files in the current directory'
						),
						array(
							array('-p', '--php'),
							'<path/to/php/binary>',
							'Path to PHP binary which must be used to run tests'
						),
						array(
							array('-drt', '--default-report-title'),
							'<string>',
							'Define default report title with <string>'
						),
						array(
							array('-sf', '--score-file'),
							'<file>',
							'Save score in file <file>'
						),
						array(
							array('-mcn', '--max-children-number'),
							'<integer>',
							'Maximum number of sub-processus which will be run simultaneously'
						),
						array(
							array('-ncc', '--no-code-coverage'),
							null,
							'Disable code coverage'
						),
						array(
							array('-nccid', '--no-code-coverage-in-directories'),
							'<directory>...',
							'Disable code coverage in directories <directory>'
						),
						array(
							array('-nccfns', '--no-code-coverage-for-namespaces'),
							'<namespace>...',
							'Disable code coverage for namespaces <namespace>'
						),
						array(
							array('-nccfc', '--no-code-coverage-for-classes'),
							'<class>...',
							'Disable code coverage for classes <class>'
						),
						array(
							array('-f', '--files'),
							'<file>...',
							'Execute all unit test files <file>'
						),
						array(
							array('-d', '--directories'),
							'<directory>...',
							'Execute unit test files in all <directory>'
						),
						array(
							array('-tfe', '--test-file-extensions'),
							'<extension>...',
							'Execute unit test files with one of extensions <extension>'
						),
						array(
							array('-g', '--glob'),
							'<pattern>...',
							'Execute unit test files which match <pattern>'
						),
						array(
							array('-t', '--tags'),
							'<tag>...',
							'Execute only unit test with tags <tag>'
						),
						array(
							array('-m', '--methods'),
							'<class::method>...',
							'Execute all <class::method>, * may be used as wildcard for class name or method name'
						),
						array(
							array('-ns', '--namespaces'),
							'<namespace>...',
							'Execute all classes in all namespaces <namespace>'
						),
						array(
							array('-l', '--loop'),
							null,
							'Execute tests in an infinite loop'
						),
						array(
							array('--test-it'),
							null,
							'Execute atoum unit tests'
						),
						array(
							array('--test-all'),
							null,
							'DEPRECATED, please do $runner->addTestsFromDirectory(\'path/to/default/tests/directory\') in a configuration file and use atoum without any argument instead'
						),
						array(
							array('-ft', '--force-terminal'),
							null,
							'Force output as in terminal'
						),
						array(
							array('-bf', '--bootstrap-file'),
							'<file>',
							'Include <file> before executing each test method'
						),
						array(
							array('-ulr', '--use-light-report'),
							null,
							'Use "light" CLI report'
						),
						array(
							array('-utr', '--use-tap-report'),
							null,
							'Use TAP report'
						),
						array(
							array('--debug'),
							null,
							'Enable debug mode'
						),
						array(
							array('-xc', '--xdebug-config'),
							null,
							'Set XDEBUG_CONFIG variable'
						)
					)
				)
			->if($runner = new testedClass($name = uniqid(), $adapter = new atoum\adapter()))
			->then
				->string($runner->getName())->isEqualTo($name)
				->object($runner->getAdapter())->isIdenticalTo($adapter)
				->object($runner->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($runner->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->object($runner->getRunner())->isInstanceOf('mageekguy\atoum\runner')
				->variable($runner->getScoreFile())->isNull()
				->array($runner->getArguments())->isEmpty()
				->array($runner->getHelp())->isEqualTo(array(
						array(
							array('-h', '--help'),
							null,
							'Display this help'
						),
						array(
							array('-c', '--configurations'),
							'<file>...',
							'Use all configuration files <file>'
						),
						array(
							array('-v', '--version'),
							null,
							'Display version'
						),
						array(
							array('+verbose', '++verbose'),
							null,
							'Enable verbose mode'
						),
						array(
							array('--init'),
							null,
							'Create configuration and bootstrap files in the current directory'
						),
						array(
							array('-p', '--php'),
							'<path/to/php/binary>',
							'Path to PHP binary which must be used to run tests'
						),
						array(
							array('-drt', '--default-report-title'),
							'<string>',
							'Define default report title with <string>'
						),
						array(
							array('-sf', '--score-file'),
							'<file>',
							'Save score in file <file>'
						),
						array(
							array('-mcn', '--max-children-number'),
							'<integer>',
							'Maximum number of sub-processus which will be run simultaneously'
						),
						array(
							array('-ncc', '--no-code-coverage'),
							null,
							'Disable code coverage'
						),
						array(
							array('-nccid', '--no-code-coverage-in-directories'),
							'<directory>...',
							'Disable code coverage in directories <directory>'
						),
						array(
							array('-nccfns', '--no-code-coverage-for-namespaces'),
							'<namespace>...',
							'Disable code coverage for namespaces <namespace>'
						),
						array(
							array('-nccfc', '--no-code-coverage-for-classes'),
							'<class>...',
							'Disable code coverage for classes <class>'
						),
						array(
							array('-f', '--files'),
							'<file>...',
							'Execute all unit test files <file>'
						),
						array(
							array('-d', '--directories'),
							'<directory>...',
							'Execute unit test files in all <directory>'
						),
						array(
							array('-tfe', '--test-file-extensions'),
							'<extension>...',
							'Execute unit test files with one of extensions <extension>'
						),
						array(
							array('-g', '--glob'),
							'<pattern>...',
							'Execute unit test files which match <pattern>'
						),
						array(
							array('-t', '--tags'),
							'<tag>...',
							'Execute only unit test with tags <tag>'
						),
						array(
							array('-m', '--methods'),
							'<class::method>...',
							'Execute all <class::method>, * may be used as wildcard for class name or method name'
						),
						array(
							array('-ns', '--namespaces'),
							'<namespace>...',
							'Execute all classes in all namespaces <namespace>'
						),
						array(
							array('-l', '--loop'),
							null,
							'Execute tests in an infinite loop'
						),
						array(
							array('--test-it'),
							null,
							'Execute atoum unit tests'
						),
						array(
							array('--test-all'),
							null,
							'DEPRECATED, please do $runner->addTestsFromDirectory(\'path/to/default/tests/directory\') in a configuration file and use atoum without any argument instead'
						),
						array(
							array('-ft', '--force-terminal'),
							null,
							'Force output as in terminal'
						),
						array(
							array('-bf', '--bootstrap-file'),
							'<file>',
							'Include <file> before executing each test method'
						),
						array(
							array('-ulr', '--use-light-report'),
							null,
							'Use "light" CLI report'
						),
						array(
							array('-utr', '--use-tap-report'),
							null,
							'Use TAP report'
						),
						array(
							array('--debug'),
							null,
							'Enable debug mode'
						),
						array(
							array('-xc', '--xdebug-config'),
							null,
							'Set XDEBUG_CONFIG variable'
						)
					)
				)
		;
	}

	public function testSetArguments()
	{
		$this
			->if($runner = new testedClass($name = uniqid()))
			->then
				->object($runner->setArguments(array()))->isIdenticalTo($runner)
				->array($runner->getArguments())->isEmpty()
				->object($runner->setArguments($arguments = array(uniqid(), uniqid(), uniqid())))->isIdenticalTo($runner)
				->array($runner->getArguments())->isEqualTo($arguments)
		;
	}

	public function testUseConfigFile()
	{
		$this
			->if($runner = new testedClass(uniqid()))
			->and($runner->setLocale($locale = new \mock\mageekguy\atoum\locale()))
			->then
				->exception(function() use ($runner, & $file) {
						$runner->useConfigFile($file = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\includer\exception')
					->hasMessage('Unable to find configuration file \'' . $file . '\'')
				->mock($locale)->call('_')->withArguments('Unable to find configuration file \'%s\'')->once()
			->if($configFile = stream::get())
			->and($configFile->file_get_contents = '<?php $runner->disableCodeCoverage(); ?>')
			->then
				->boolean($runner->getRunner()->codeCoverageIsEnabled())->isTrue()
				->object($runner->useConfigFile((string) $configFile))->isIdenticalTo($runner)
				->boolean($runner->getRunner()->codeCoverageIsEnabled())->isFalse()
		;
	}

	public function testAddTestAllDirectory()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->and($stderr = new \mock\mageekguy\atoum\writers\std\err())
			->and($this->calling($stderr)->clear = $stderr)
			->and($this->calling($stderr)->write = function() {})
			->and($runner->setErrorWriter($stderr))
			->then
				->object($runner->addTestAllDirectory(uniqid()))->isIdenticalTo($runner)
				->mock($stderr)->call('write')->withArguments('Error: --test-all argument is deprecated, please replace call to mageekguy\atoum\scripts\runner::addTestAllDirectory(\'path/to/default/tests/directory\') by $runner->addTestsFromDirectory(\'path/to/default/tests/directory\') in your configuration files and use atoum without any argument instead' . PHP_EOL)->once()
		;
	}

	public function testAddDefaultReport()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->then
				->object($report = $runner->addDefaultReport())->isInstanceOf('mageekguy\atoum\reports\realtime\cli')
				->array($report->getWriters())->isEqualTo(array(new atoum\writers\std\out()))
		;
	}

	public function testAddReport()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->then
				->object($runner->addReport($report = new \mock\mageekguy\atoum\report()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report))
				->object($runner->addReport($otherReport = new \mock\mageekguy\atoum\report()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report, $otherReport))
		;
	}

	public function testSetReport()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->then
				->object($runner->setReport($report = new \mock\mageekguy\atoum\report()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($report))
				->object($runner->setReport($otherReport = new \mock\mageekguy\atoum\report()))->isIdenticalTo($runner)
				->array($runner->getReports())->isEqualTo(array($otherReport))
			->if($runner->addReport($report))
			->then
				->array($runner->getReports())->isEqualTo(array($otherReport))
		;
	}

	public function testSetNamespaces()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->then
				->object($runner->testNamespaces(array()))->isIdenticalTo($runner)
				->array($runner->getTestedNamespaces())->isEmpty()
				->object($runner->testNamespaces(array('foo', '\bar', 'foo\bar\\', '\this\is\a\namespace\\')))->isIdenticalTo($runner)
				->array($runner->getTestedNamespaces())->isEqualTo(array('foo', 'bar', 'foo\bar', 'this\is\a\namespace'))
		;
	}

	public function testAddDefaultArguments()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->then
				->object($runner->addDefaultArguments($arg1 = uniqid()))->isInstanceOf($runner)
				->boolean($runner->hasDefaultArguments())->isTrue()
				->array($runner->getDefaultArguments())->isEqualTo(array($arg1))
				->object($runner->addDefaultArguments($arg2 = uniqid(), $arg3 = uniqid()))->isInstanceOf($runner)
				->boolean($runner->hasDefaultArguments())->isTrue()
				->array($runner->getDefaultArguments())->isEqualTo(array($arg1, $arg2, $arg3))
		;
	}

	public function testInit()
	{
		$this
			->given($runner = new testedClass(uniqid()))
			->and($runner->setAdapter($adapter = new atoum\test\adapter()))
			->and($runner->setOutputWriter($outputWriter = new \mock\mageekguy\atoum\writers\std\out()))
			->and($runner->setPrompt($prompt = new \mock\mageekguy\atoum\script\prompt()))
			->and($adapter->copy = true)
			->and($adapter->getcwd = $currentDirectory = uniqid())
			->and($adapter->file_exists = false)
			->and($this->calling($outputWriter)->write = function() {})
			->then
				->object($runner->init())->isIdenticalTo($runner)
				->mock($prompt)
					->call('ask')
						->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in the current directory, type \'Y\' to overwrite it...')->never()
				->mock($outputWriter)
					->call('write')
						->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in the current directory' . PHP_EOL)->once()
						->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in the current directory' . PHP_EOL)->once()
				->adapter($adapter)
					->call('copy')
						->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->once()
						->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->once()
			->if($this->resetAdapter($adapter))
			->and($this->resetMock($outputWriter))
			->and($adapter->file_exists = true)
			->and($this->calling($prompt)->ask = 'Y')
			->then
				->object($runner->init())->isIdenticalTo($runner)
				->mock($prompt)
					->call('ask')
						->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in the current directory, type \'Y\' to overwrite it...')->once()
						->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' already exists in the current directory, type \'Y\' to overwrite it...')->once()
				->mock($outputWriter)
					->call('write')
						->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in the current directory' . PHP_EOL)->once()
						->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in the current directory' . PHP_EOL)->once()
				->adapter($adapter)
					->call('copy')
						->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->once()
						->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->once()
			->if($this->resetAdapter($adapter))
			->and($this->resetMock($outputWriter))
			->and($this->resetMock($prompt))
			->and($adapter->file_exists = true)
			->and($this->calling($prompt)->ask = 'y')
			->then
				->object($runner->init())->isIdenticalTo($runner)
				->mock($prompt)
					->call('ask')
						->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in the current directory, type \'Y\' to overwrite it...')->once()
						->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' already exists in the current directory, type \'Y\' to overwrite it...')->once()
				->mock($outputWriter)
					->call('write')
						->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in the current directory' . PHP_EOL)->never()
						->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in the current directory' . PHP_EOL)->never()
				->adapter($adapter)
					->call('copy')
						->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->never()
						->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->never()
			->if($this->resetAdapter($adapter))
			->and($this->resetMock($outputWriter))
			->and($this->resetMock($prompt))
			->and($adapter->file_exists = true)
			->and($this->calling($prompt)->ask = uniqid())
			->then
				->object($runner->init())->isIdenticalTo($runner)
				->mock($prompt)
					->call('ask')
						->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in the current directory, type \'Y\' to overwrite it...')->once()
						->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' already exists in the current directory, type \'Y\' to overwrite it...')->once()
				->mock($outputWriter)
					->call('write')
						->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in the current directory' . PHP_EOL)->never()
						->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in the current directory' . PHP_EOL)->never()
				->adapter($adapter)
					->call('copy')
						->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->never()
						->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->never()
			->if($this->calling($prompt)->ask = 'Y')
			->and($adapter->copy = false)
			->then
				->exception(function() use ($runner) { $runner->init(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to write \'' . atoum\directory . '/resources/configurations/runner/atoum.php.dist\' to \'' . $currentDirectory . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile . '\'')
		;
	}

	public function testSetInfoWriter()
	{
		$this
			->given($runner = new testedClass(uniqid()))
			->then
				->object($runner->setInfoWriter($errorWriter = new atoum\writers\std\err()))->isIdenticalTo($runner)
				->object($runner->getInfoWriter())->isIdenticalTo($errorWriter)
			->given($colorizer = new cli\colorizer('0;32'))
			->and($defaultInfoWriter = new atoum\writers\std\out())
			->and($defaultInfoWriter->addDecorator($colorizer))
			->then
				->object($runner->setInfoWriter())->isIdenticalTo($runner)
				->object($runner->getInfoWriter())->isEqualTo($defaultInfoWriter)
		;
	}

	public function testSetWarningWriter()
	{
		$this
			->given($runner = new testedClass(uniqid()))
			->then
				->object($runner->setWarningWriter($warningWriter = new atoum\writers\std\err()))->isIdenticalTo($runner)
				->object($runner->getWarningWriter())->isIdenticalTo($warningWriter)
			->given($colorizer = new cli\colorizer('0;33'))
			->and($colorizer->setPattern('/^([^:]+:)/'))
			->and($defaultWarningWriter = new atoum\writers\std\err())
			->and($defaultWarningWriter->addDecorator($colorizer))
			->then
				->object($runner->setWarningWriter())->isIdenticalTo($runner)
				->object($runner->getWarningWriter())->isEqualTo($defaultWarningWriter)
		;
	}

	public function testSetErrorWriter()
	{
		$this
			->given($runner = new testedClass(uniqid()))
			->then
				->object($runner->setErrorWriter($errorWriter = new atoum\writers\std\err()))->isIdenticalTo($runner)
				->object($runner->getErrorWriter())->isIdenticalTo($errorWriter)
			->given($colorizer = new cli\colorizer('0;31'))
			->and($colorizer->setPattern('/^([^:]+:)/'))
			->and($defaultErrorWriter = new atoum\writers\std\err())
			->and($defaultErrorWriter->addDecorator($colorizer))
			->then
				->object($runner->setErrorWriter())->isIdenticalTo($runner)
				->object($runner->getErrorWriter())->isEqualTo($defaultErrorWriter)
		;
	}

	public function testHelp()
	{
		$this
			->if($argumentsParser = new mock\script\arguments\parser())
			->and($this->calling($argumentsParser)->addHandler = function() {})
			->and($locale = new mock\locale())
			->and($this->calling($locale)->_ = function($string) { return $string; })
			->and($helpWriter = new mock\writers\std\out())
			->and($this->calling($helpWriter)->write = function() {})
			->and($runner = new testedClass($name = uniqid()))
			->and($runner->setArgumentsParser($argumentsParser))
			->and($runner->setLocale($locale))
			->and($runner->setHelpWriter($helpWriter))
			->then
				->object($runner->help())->isIdenticalTo($runner)
				->mock($helpWriter)->call('write')
					->atLeastOnce()
					->withArguments('Usage: ' . $name . ' [path/to/test/file] [options]' . PHP_EOL)->once()
		;
	}

	public function testRun()
	{
		$this
			->if($locale = new mock\locale())
			->and($this->calling($locale)->_ = function($string) { return $string; })
			->and($helpWriter = new mock\writers\std\out())
			->and($this->calling($helpWriter)->write = function() {})
			->and($errorWriter = new mock\writers\std\err())
			->and($this->calling($errorWriter)->clear = $errorWriter)
			->and($this->calling($errorWriter)->write = $errorWriter)
			->and($runner = new mock\runner())
			->and($this->calling($runner)->getTestPaths = array())
			->and($this->calling($runner)->getDeclaredTestClasses = array())
			->and($this->calling($runner)->run = function() {})
			->and($script = new testedClass($name = uniqid()))
			->and($script->setLocale($locale))
			->and($script->setHelpWriter($helpWriter))
			->and($script->setErrorWriter($errorWriter))
			->and($script->setRunner($runner))
			->then
				->object($script->run())->isIdenticalTo($script)
				->mock($locale)
					->call('_')
						->withArguments('Error: %s')->once()
						->withArguments('No test found')->once()
				->mock($errorWriter)
					->call('clear')->once()
					->call('write')->withArguments('Error: No test found' . PHP_EOL)->once()
		;
	}

	public function testAutorun()
	{
		$this
			->if($script = new testedClass(uniqid()))
			->and($script->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->realpath = function($path) { return $path; })
			->when(function() { if (isset($_SERVER['argv']) === true) { unset($_SERVER['argv']); } })
			->then
				->boolean($script->autorun())->isTrue()
			->if($_SERVER['argv'] = array())
			->then
				->boolean($script->autorun())->isTrue()
			->if($_SERVER['argv'][0] = $script->getName())
			->then
				->boolean($script->autorun())->isFalse()
			->if($adapter->realpath = uniqid())
			->then
				->boolean($script->autorun())->isTrue()
		;
	}
}
