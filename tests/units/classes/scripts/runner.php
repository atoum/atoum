<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\scripts,
	mageekguy\atoum\scripts\builder\vcs
;

require_once __DIR__ . '/../../runner.php';

class runner extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\script')
		;
	}

	public function testClassConstants()
	{
		$this->assert
			->string(scripts\runner::defaultConfigFile)->isEqualTo('.atoum.php')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($scriptRunner = new scripts\runner($name = uniqid()))
			->then
				->string($scriptRunner->getName())->isEqualTo($name)
				->object($scriptRunner->getAdapter())->isEqualTo(new atoum\adapter())
				->object($scriptRunner->getLocale())->isEqualTo(new atoum\locale())
				->object($scriptRunner->getRunner())->isEqualTo(new atoum\runner())
				->object($scriptRunner->getIncluder())->isEqualTo(new atoum\includer())
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
							array('-c', '--configuration-files'),
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
							array('-f', '--test-files'),
							'<file>...',
							'Execute all unit test files <file>'
						),
						array(
							array('-d', '--directories'),
							'<directory>...',
							'Execute unit test files in all <directory>'
						),
						array(
							array('-g', '--glob-filter'),
							'<pattern>...',
							'Execute unit test files matching <pattern>'
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
			->and($factory->returnWhenBuild('atoum\locale', $locale = new atoum\locale()))
			->and($factory->returnWhenBuild('atoum\adapter', $adapter = new atoum\adapter()))
			->and($factory->returnWhenBuild('atoum\runner', $runner = new atoum\runner()))
			->and($factory->returnWhenBuild('atoum\includer', $includer = new atoum\includer()))
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
							array('-c', '--configuration-files'),
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
							array('-f', '--test-files'),
							'<file>...',
							'Execute all unit test files <file>'
						),
						array(
							array('-d', '--directories'),
							'<directory>...',
							'Execute unit test files in all <directory>'
						),
						array(
							array('-g', '--glob-filter'),
							'<pattern>...',
							'Execute unit test files matching <pattern>'
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
		$runner = new scripts\runner($name = uniqid());

		$this->assert
			->object($runner->setArguments(array()))->isIdenticalTo($runner)
			->array($runner->getArguments())->isEmpty()
			->object($runner->setArguments($arguments = array(uniqid(), uniqid(), uniqid())))->isIdenticalTo($runner)
			->array($runner->getArguments())->isEqualTo($arguments)
		;
	}

	public function testUseConfigFile()
	{
		$factory = new atoum\factory();
		$factory->returnWhenBuild('mageekguy\atoum\locale', $locale = new \mock\mageekguy\atoum\locale());

		$runner = new scripts\runner($name = uniqid(), $factory);

		$this->assert
			->exception(function() use ($runner, & $file) {
					$runner->useConfigFile($file = uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\includer\exception')
				->hasMessage('Unable to find configuration file \'' . $file . '\'')
			->mock($locale)->call('_')->withArguments('Unable to find configuration file \'%s\'')->once()
		;

		$streamController = atoum\mock\stream::get('includeWithoutOutput');
		$streamController->file_get_contents = '<?php $runner->disableCodeCoverage(); ?>';

		$this->assert
			->boolean($runner->getRunner()->codeCoverageIsEnabled())->isTrue()
			->object($runner->useConfigFile('atoum://includeWithoutOutput'))->isIdenticalTo($runner)
			->boolean($runner->getRunner()->codeCoverageIsEnabled())->isFalse()
		;
	}

	public function testUseDefaultConfigFiles()
	{
		$this
			->assert
				->if($runner = new \mock\mageekguy\atoum\scripts\runner($name = uniqid()))
				->and($runner->getMockController()->useConfigFile = function() {})
				->then
					->object($runner->useDefaultConfigFiles())->isIdenticalTo($runner)
					->mock($runner)
						->foreach(scripts\runner::getSubDirectoryPath(atoum\directory), function($mock, $path) {
								$mock->call('useConfigFile')->withArguments($path . scripts\runner::defaultConfigFile)->once();
							}
						)
		;
	}

	public function testGetSubDirectoryPath()
	{
		$this->assert
			->array(scripts\runner::getSubDirectoryPath(''))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('', '/'))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('', '\\'))->isEmpty()
			->array(scripts\runner::getSubDirectoryPath('/', '/'))->isEqualTo(array('/'))
			->array(scripts\runner::getSubDirectoryPath('/toto', '/'))->isEqualTo(array('/', '/toto/'))
			->array(scripts\runner::getSubDirectoryPath('/toto/', '/'))->isEqualTo(array('/', '/toto/'))
			->array(scripts\runner::getSubDirectoryPath('/toto/tutu', '/'))->isEqualTo(array('/', '/toto/', '/toto/tutu/'))
			->array(scripts\runner::getSubDirectoryPath('/toto/tutu/', '/'))->isEqualTo(array('/', '/toto/', '/toto/tutu/'))
			->array(scripts\runner::getSubDirectoryPath('c:\\', '\\'))->isEqualTo(array('c:\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\toto', '\\'))->isEqualTo(array('c:\\', 'c:\toto\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\toto\\', '\\'))->isEqualTo(array('c:\\', 'c:\toto\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\toto\tutu', '\\'))->isEqualTo(array('c:\\', 'c:\toto\\', 'c:\toto\tutu\\'))
			->array(scripts\runner::getSubDirectoryPath('c:\toto\tutu\\', '\\'))->isEqualTo(array('c:\\', 'c:\toto\\', 'c:\toto\tutu\\'))
		;
	}
}

?>
