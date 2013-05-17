<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream,
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
							array('-p', '--php'),
							'<path/to/php/binary>',
							'Path to PHP binary which must be used to run tests'
						),
						array(
							array('--init'),
							null,
							'Create configuration and bootstrap files'
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
							'Execute unit tests in directories defined via $script->addTestAllDirectory(\'path/to/directory\') in a configuration file'
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
							array('-p', '--php'),
							'<path/to/php/binary>',
							'Path to PHP binary which must be used to run tests'
						),
						array(
							array('--init'),
							null,
							'Create configuration and bootstrap files'
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
							'Execute unit tests in directories defined via $script->addTestAllDirectory(\'path/to/directory\') in a configuration file'
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
						)
					)
				)
		;
	}

	public function testInit()
	{
		if (defined('STDIN') === false)
		{
			define('STDIN', rand(1, PHP_INT_MAX));
		}

		$this
			->if($adapter = new atoum\test\adapter())
			->and($runner = new scripts\runner($name = uniqid(), $adapter))
			->and($runner->getPrompt()->setAdapter($adapter))
			->and($stdOut = new \mock\mageekguy\atoum\writers\std\out())
			->and($runner->setOutputWriter($stdOut))
			->and($runner->getPrompt()->setOutputWriter($stdOut))
			->then
				->object($runner->getPrompt()->getAdapter())->isIdenticalTo($adapter)
				->object($runner->getPrompt()->getOutputWriter())->isIdenticalTo($stdOut)

			->if($adapter->getcwd = $cwd = '/tmp')
			->and($adapter->is_writable = false)
			->then
				->exception(
					function() use($runner)
					{
						$runner->init();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to write in \'' . $cwd . '\' directory')

			->if($adapter->resetCalls())
			->and($adapter->is_writable = true)
			->and($adapter->file_exists = false)
			->and($adapter->copy = function() use($runner) { return $runner; })
			->and($stdOut->getMockController()->write = function() {})
			->then
				->object($runner->init())
					->isIdenticalTo($runner)
				->adapter($adapter)->call('copy')
					->withIdenticalArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', $cwd . '/.atoum.php')->once()
					->withIdenticalArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', $cwd . '/.bootstrap.php')->once()
				->mock($stdOut)->call('write')
					->withIdenticalArguments($cwd . '/.atoum.php was successfully generated' . PHP_EOL)->once()
					->withIdenticalArguments($cwd . '/.bootstrap.php was successfully generated' . PHP_EOL)->once()

			->if($adapter->resetCalls())
			->and($adapter->file_exists = true)
			->and($adapter->fgets = 'y')
			->and($stdOut->getMockController()->resetCalls())
			->then
				->object($runner->init())
					->isIdenticalTo($runner)
				->adapter($adapter)->call('copy')
					->withIdenticalArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', $cwd . '/.atoum.php')->once()
					->withIdenticalArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', $cwd . '/.bootstrap.php')->once()
				->mock($stdOut)->call('write')
					->withIdenticalArguments($cwd . '/.atoum.php already exists. Do you want to overwrite it ? (y/n) [n]')->once()
					->withIdenticalArguments($cwd . '/.atoum.php was successfully generated' . PHP_EOL)->once()
					->withIdenticalArguments($cwd . '/.bootstrap.php already exists. Do you want to overwrite it ? (y/n) [n]')->once()
					->withIdenticalArguments($cwd . '/.bootstrap.php was successfully generated' . PHP_EOL)->once()

			->if($adapter->resetCalls())
			->and($adapter->file_exists = true)
			->and($adapter->fgets = 'n')
			->and($stdOut->getMockController()->resetCalls())
			->then
				->object($runner->init())
					->isIdenticalTo($runner)
				->adapter($adapter)->call('copy')
					->withIdenticalArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', $cwd . '/.atoum.php')->never()
					->withIdenticalArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', $cwd . '/.bootstrap.php')->never()
				->mock($stdOut)->call('write')
					->withIdenticalArguments($cwd . '/.atoum.php already exists. Do you want to overwrite it ? (y/n) [n]')->once()
					->withIdenticalArguments($cwd . '/.bootstrap.php already exists. Do you want to overwrite it ? (y/n) [n]')->once()
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

	public function getTestAllDirectories()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->then
				->array($runner->getTestAllDirectories())->isEmpty()
		;
	}

	public function testAddTestAllDirectory()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->then
				->object($runner->addTestAllDirectory($directory = uniqid()))->isIdenticalTo($runner)
				->array($runner->getTestAllDirectories())->isEqualTo(array($directory))
				->object($runner->addtestalldirectory($directory))->isidenticalto($runner)
				->array($runner->gettestalldirectories())->isequalto(array($directory))
				->object($runner->addtestalldirectory(($otherDirectory = uniqid()) . DIRECTORY_SEPARATOR))->isidenticalto($runner)
				->array($runner->gettestalldirectories())->isequalto(array($directory, $otherDirectory))
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
}
