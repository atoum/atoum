<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class coverage extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass
				->extends('mageekguy\atoum\scripts\runner');
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance($name = uniqid()))
			->then
				->boolean($this->testedInstance->hasDefaultArguments())->isFalse()
				->array($this->testedInstance->getDefaultArguments())->isEmpty()
				->string($this->testedInstance->getName())->isEqualTo($name)
				->string($this->testedInstance->getReportFormat())->isEqualTo('xml')
				->object($this->testedInstance->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($this->testedInstance->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($this->testedInstance->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->object($this->testedInstance->getRunner())->isInstanceOf('mageekguy\atoum\runner')
				->variable($this->testedInstance->getScoreFile())->isNull()
				->array($this->testedInstance->getReports())->isEmpty()
				->array($this->testedInstance->getArguments())->isEmpty()
				->array($this->testedInstance->getHelp())->isEqualTo(array(
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
							'<path/to/directory>',
							sprintf($this->testedInstance->getLocale()->_('Create configuration and bootstrap files in <path/to/directory> (Optional, default: %s)'), $this->testedInstance->getDirectory())
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
							array('-nccfm', '--no-code-coverage-for-methods'),
							'<method>...',
							'Disable code coverage for methods <method>'
						),
						array(
							array('-ebpc', '--enable-branch-and-path-coverage'),
							null,
							'Enable branch and path coverage'
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
							array('-af', '--autoloader-file'),
							'<file>',
							'Include autoloader <file> before executing each test method'
						),
						array(
							array('-bf', '--bootstrap-file'),
							'<file>',
							'Include bootstrap <file> before executing each test method'
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
						),
						array(
							array('-fivm', '--fail-if-void-methods'),
							null,
							'Make the test suite fail if there is at least one void test method'
						),
						array(
							array('-fism', '--fail-if-skipped-methods'),
							null,
							'Make the test suite fail if there is at least one skipped test method'
						),
						array(
							array('-fmt', '--format'),
							'<xml|clover|html|treemap>',
							'Coverage report format'
						),
						array(
							array('-o', '--output'),
							'<path/to/file/or/directory>',
							'Coverage report output path'
						)
					)
				)
			->if($this->newTestedInstance($name = uniqid(), $adapter = new atoum\adapter()))
			->then
				->string($this->testedInstance->getName())->isEqualTo($name)
				->string($this->testedInstance->getReportFormat())->isEqualTo('xml')
				->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
				->object($this->testedInstance->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($this->testedInstance->getIncluder())->isInstanceOf('mageekguy\atoum\includer')
				->object($this->testedInstance->getRunner())->isInstanceOf('mageekguy\atoum\runner')
				->variable($this->testedInstance->getScoreFile())->isNull()
				->array($this->testedInstance->getArguments())->isEmpty()
				->array($this->testedInstance->getHelp())->isEqualTo(array(
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
							'<path/to/directory>',
							sprintf($this->testedInstance->getLocale()->_('Create configuration and bootstrap files in <path/to/directory> (Optional, default: %s)'), $this->testedInstance->getDirectory())
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
							array('-nccfm', '--no-code-coverage-for-methods'),
							'<method>...',
							'Disable code coverage for methods <method>'
						),
						array(
							array('-ebpc', '--enable-branch-and-path-coverage'),
							null,
							'Enable branch and path coverage'
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
							array('-af', '--autoloader-file'),
							'<file>',
							'Include autoloader <file> before executing each test method'
						),
						array(
							array('-bf', '--bootstrap-file'),
							'<file>',
							'Include bootstrap <file> before executing each test method'
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
						),
						array(
							array('-fivm', '--fail-if-void-methods'),
							null,
							'Make the test suite fail if there is at least one void test method'
						),
						array(
							array('-fism', '--fail-if-skipped-methods'),
							null,
							'Make the test suite fail if there is at least one skipped test method'
						),
						array(
							array('-fmt', '--format'),
							'<xml|clover|html|treemap>',
							'Coverage report format'
						),
						array(
							array('-o', '--output'),
							'<path/to/file/or/directory>',
							'Coverage report output path'
						)
					)
				)
		;
	}
}
