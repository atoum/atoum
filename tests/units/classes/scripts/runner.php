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
						)
					)
				)
			->if($scriptRunner = new scripts\runner($name = uniqid(), $locale = new atoum\locale(), $adapter = new atoum\adapter(), $runner = new atoum\runner(), $includer = new atoum\includer()))
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
		$this->mock('mageekguy\atoum\locale');

		$runner = new scripts\runner($name = uniqid(), $locale = new \mock\mageekguy\atoum\locale());

		$this->assert
			->exception(function() use ($runner, & $file) {
					$runner->useConfigFile($file = uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\includer\exception')
				->hasMessage('Unable to find configuration file \'' . $file . '\'')
			->mock($locale)->call('_')->withArguments('Unable to find configuration file \'%s\'')->once()
		;

		$streamController = atoum\mock\stream::get('includeWithOutput');
		$streamController->file_get_contents = $output = uniqid();

		$this->assert
			->exception(function() use ($runner) {
					$runner->useConfigFile('atoum://includeWithOutput');
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('There is output \'' . $output . '\' in configuration file \'atoum://includeWithOutput\'')
			->mock($locale)->call('_')->withArguments('There is output \'%s\' in configuration file \'%s\'')->once()
		;

		$streamController = atoum\mock\stream::get('includeWithoutOutput');
		$streamController->file_get_contents = '<?php $runner->disableCodeCoverage(); ?>';

		$this->assert
			->boolean($runner->getRunner()->codeCoverageIsEnabled())->isTrue()
			->object($runner->useConfigFile('atoum://includeWithoutOutput'))->isIdenticalTo($runner)
			->boolean($runner->getRunner()->codeCoverageIsEnabled())->isFalse()
		;
	}

	public function testUseDefaultConfigFile()
	{
		$this->mock('mageekguy\atoum\scripts\runner');

		$runner = new \mock\mageekguy\atoum\scripts\runner($name = uniqid());
		$runner->getMockController()->useConfigFile = function() {};

		$this->assert
			->object($runner->useDefaultConfigFile())->isIdenticalTo($runner)
			->mock($runner)->call('useConfigFile')->withArguments(atoum\directory . '/' . scripts\runner::defaultConfigFile)->once()
		;
	}
}

?>
