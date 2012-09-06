<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\scripts,
	mageekguy\atoum\mock\stream,
	mageekguy\atoum\scripts\builder\vcs
;

require_once __DIR__ . '/../../runner.php';

class runner extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\script');
	}

	public function testClassConstants()
	{
		$this->assert
			->string(scripts\runner::defaultConfigFile)->isEqualTo('.atoum.php')
		;
	}

	public function test__construct()
	{
		$this
			->if($scriptRunner = new scripts\runner($name = uniqid()))
			->then
				->string($scriptRunner->getName())->isEqualTo($name)
				->object($scriptRunner->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($scriptRunner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($scriptRunner->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->object($scriptRunner->getRunner())->isInstanceOf('mageekguy\atoum\runner')
				->object($scriptRunner->getRunner()->getFactory())->isIdenticalTo($scriptRunner->getFactory())
				->object($scriptRunner->getFactory()->build('mageekguy\atoum\locale'))->isIdenticalTo($scriptRunner->getLocale())
				->object($scriptRunner->getFactory()->build('mageekguy\atoum\adapter'))->isIdenticalTo($scriptRunner->getAdapter())
				->object($scriptRunner->getFactory()->build('mageekguy\atoum\includer'))->isIdenticalTo($scriptRunner->getIncluder())
				->variable($scriptRunner->getScoreFile())->isNull()
				->array($scriptRunner->getArguments())->isEmpty()
				->array($scriptRunner->getHelp())->isEqualTo(array(
						array(
							array('-h', '--help'),
							null,
							'Display this help'
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
							array('-drt', '--default-report-title'),
							'<string>',
							'Define default report title with <string>'
						),
						array(
							array('-c', '--configurations'),
							'<file>...',
							'Use all configuration files <file>'
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
						)
					)
				)
			->if($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory['mageekguy\atoum\locale'] = $locale = new atoum\locale())
			->and($factory['mageekguy\atoum\adapter'] = $adapter = new atoum\adapter())
			->and($factory['mageekguy\atoum\runner'] = $runner = new atoum\runner())
			->and($factory['mageekguy\atoum\includer'] = $includer = new atoum\includer())
			->and($scriptRunner = new scripts\runner($name = uniqid(), $factory))
			->then
				->string($scriptRunner->getName())->isEqualTo($name)
				->object($scriptRunner->getAdapter())->isIdenticalTo($adapter)
				->object($scriptRunner->getLocale())->isIdenticalTo($locale)
				->object($scriptRunner->getRunner())->isIdenticalTo($runner)
				->object($scriptRunner->getIncluder())->isIdenticalTo($includer)
				->variable($scriptRunner->getScoreFile())->isNull()
				->array($scriptRunner->getArguments())->isEmpty()
				->array($scriptRunner->getHelp())->isEqualTo(array(
						array(
							array('-h', '--help'),
							null,
							'Display this help'
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
							array('-drt', '--default-report-title'),
							'<string>',
							'Define default report title with <string>'
						),
						array(
							array('-c', '--configurations'),
							'<file>...',
							'Use all configuration files <file>'
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
						)
					)
				)
		;
	}

	public function testSetArguments()
	{
		$this
			->if($runner = new scripts\runner($name = uniqid()))
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
			->if($factory = new atoum\factory())
			->and($factory['mageekguy\atoum\locale'] = $locale = new \mock\mageekguy\atoum\locale())
			->and($runner = new scripts\runner(uniqid(), $factory))
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

	public function testUseDefaultConfigFiles()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->and($runner->getMockController()->useConfigFile = function() {})
			->then
				->object($runner->useDefaultConfigFiles(atoum\directory))->isIdenticalTo($runner)
				->mock($runner)
					->foreach(scripts\runner::getSubDirectoryPath(atoum\directory), function($mock, $path) {
							$mock->call('useConfigFile')->withArguments($path . scripts\runner::defaultConfigFile)->once();
						}
					)
			->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
			->and($runner->getMockController()->useConfigFile = function() {})
			->and($runner->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->getcwd = $workingDirectory = uniqid() . DIRECTORY_SEPARATOR . uniqid() . DIRECTORY_SEPARATOR . uniqid())
			->then
				->object($runner->useDefaultConfigFiles())->isIdenticalTo($runner)
				->mock($runner)
					->foreach(scripts\runner::getSubDirectoryPath($workingDirectory), function($mock, $path) {
						$mock->call('useConfigFile')->withArguments($path . scripts\runner::defaultConfigFile)->once();
					}
				)
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

	public function testGetSubDirectoryPath()
	{
		$this
			->array(scripts\runner::getSubDirectoryPath(''))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('', '/'))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('', '\\'))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('/', '/'))->isEqualTo(array('/'))
			->array(scripts\runner::getSubDirectoryPath('/foo', '/'))->isEqualTo(array('/', '/foo/'))
			->array(scripts\runner::getSubDirectoryPath('/foo/', '/'))->isEqualTo(array('/', '/foo/'))
			->array(scripts\runner::getSubDirectoryPath('/foo/bar', '/'))->isEqualTo(array('/', '/foo/', '/foo/bar/'))
			->array(scripts\runner::getSubDirectoryPath('/foo/bar/', '/'))->isEqualTo(array('/', '/foo/', '/foo/bar/'))
			->array(scripts\runner::getSubDirectoryPath('c:\\', '\\'))->isEqualTo(array('c:\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\foo', '\\'))->isEqualTo(array('c:\\', 'c:\foo\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\foo\\', '\\'))->isEqualTo(array('c:\\', 'c:\foo\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\foo\bar', '\\'))->isEqualTo(array('c:\\', 'c:\foo\\', 'c:\foo\bar\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\foo\bar\\', '\\'))->isEqualTo(array('c:\\', 'c:\foo\\', 'c:\foo\bar\\'))
		;
	}
}
