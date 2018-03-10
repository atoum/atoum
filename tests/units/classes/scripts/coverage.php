<?php

namespace mageekguy\atoum\tests\units\scripts;

use mageekguy\atoum;

require_once __DIR__ . '/../../runner.php';

class coverage extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(atoum\scripts\runner::class);
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
                ->object($this->testedInstance->getAdapter())->isInstanceOf(atoum\adapter::class)
                ->object($this->testedInstance->getLocale())->isInstanceOf(atoum\locale::class)
                ->object($this->testedInstance->getIncluder())->isInstanceOf(atoum\includer::class)
                ->object($this->testedInstance->getRunner())->isInstanceOf(atoum\runner::class)
                ->variable($this->testedInstance->getScoreFile())->isNull()
                ->array($this->testedInstance->getReports())->isEmpty()
                ->array($this->testedInstance->getArguments())->isEmpty()
                ->array($this->testedInstance->getHelp())->isEqualTo(
                    [
                        [
                            ['-h', '--help'],
                            null,
                            'Display this help'
                        ],
                        [
                            ['-c', '--configurations'],
                            '<file>...',
                            'Use all configuration files <file>'
                        ],
                        [
                            ['-v', '--version'],
                            null,
                            'Display version'
                        ],
                        [
                            ['+verbose', '++verbose'],
                            null,
                            'Enable verbose mode'
                        ],
                        [
                            ['--init'],
                            '<path/to/directory>',
                            sprintf($this->testedInstance->getLocale()->_('Create configuration and bootstrap files in <path/to/directory> (Optional, default: %s)'), $this->testedInstance->getDirectory())
                        ],
                        [
                            ['-p', '--php'],
                            '<path/to/php/binary>',
                            'Path to PHP binary which must be used to run tests'
                        ],
                        [
                            ['-drt', '--default-report-title'],
                            '<string>',
                            'Define default report title with <string>'
                        ],
                        [
                            ['-sf', '--score-file'],
                            '<file>',
                            'Save score in file <file>'
                        ],
                        [
                            ['-mcn', '--max-children-number'],
                            '<integer>',
                            'Maximum number of sub-processes which will be run simultaneously'
                        ],
                        [
                            ['-ncc', '--no-code-coverage'],
                            null,
                            'Disable code coverage'
                        ],
                        [
                            ['-nccid', '--no-code-coverage-in-directories'],
                            '<directory>...',
                            'Disable code coverage in directories <directory>'
                        ],
                        [
                            ['-nccfns', '--no-code-coverage-for-namespaces'],
                            '<namespace>...',
                            'Disable code coverage for namespaces <namespace>'
                        ],
                        [
                            ['-nccfc', '--no-code-coverage-for-classes'],
                            '<class>...',
                            'Disable code coverage for classes <class>'
                        ],
                        [
                            ['-nccfm', '--no-code-coverage-for-methods'],
                            '<method>...',
                            'Disable code coverage for methods <method>'
                        ],
                        [
                            ['-ebpc', '--enable-branch-and-path-coverage'],
                            null,
                            'Enable branch and path coverage'
                        ],
                        [
                            ['-f', '--files'],
                            '<file>...',
                            'Execute all unit test files <file>'
                        ],
                        [
                            ['-d', '--directories'],
                            '<directory>...',
                            'Execute unit test files in all <directory>'
                        ],
                        [
                            ['-tfe', '--test-file-extensions'],
                            '<extension>...',
                            'Execute unit test files with one of extensions <extension>'
                        ],
                        [
                            ['-g', '--glob'],
                            '<pattern>...',
                            'Execute unit test files which match <pattern>'
                        ],
                        [
                            ['-t', '--tags'],
                            '<tag>...',
                            'Execute only unit test with tags <tag>'
                        ],
                        [
                            ['-m', '--methods'],
                            '<class::method>...',
                            'Execute all <class::method>, * may be used as wildcard for class name or method name'
                        ],
                        [
                            ['-ns', '--namespaces'],
                            '<namespace>...',
                            'Execute all classes in all namespaces <namespace>'
                        ],
                        [
                            ['-l', '--loop'],
                            null,
                            'Execute tests in an infinite loop'
                        ],
                        [
                            ['--test-it'],
                            null,
                            'Execute atoum unit tests'
                        ],
                        [
                            ['-ft', '--force-terminal'],
                            null,
                            'Force output as in terminal'
                        ],
                        [
                            ['-af', '--autoloader-file'],
                            '<file>',
                            'Include autoloader <file> before executing each test method'
                        ],
                        [
                            ['-bf', '--bootstrap-file'],
                            '<file>',
                            'Include bootstrap <file> before executing each test method'
                        ],
                        [
                            ['-ulr', '--use-light-report'],
                            null,
                            'Use "light" CLI report'
                        ],
                        [
                            ['-udr', '--use-dot-report'],
                            null,
                            'Use "dot" CLI report'
                        ],
                        [
                            ['-utr', '--use-tap-report'],
                            null,
                            'Use TAP report'
                        ],
                        [
                            ['--debug'],
                            null,
                            'Enable debug mode'
                        ],
                        [
                            ['-xc', '--xdebug-config'],
                            null,
                            'Set XDEBUG_CONFIG variable'
                        ],
                        [
                            ['-fivm', '--fail-if-void-methods'],
                            null,
                            'Make the test suite fail if there is at least one void test method'
                        ],
                        [
                            ['-fism', '--fail-if-skipped-methods'],
                            null,
                            'Make the test suite fail if there is at least one skipped test method'
                        ],
                        [
                            ['-fmt', '--format'],
                            '<xml|clover|html|treemap>',
                            'Coverage report format'
                        ],
                        [
                            ['-o', '--output'],
                            '<path/to/file/or/directory>',
                            'Coverage report output path'
                        ]
                    ]
                )
            ->if($this->newTestedInstance($name = uniqid(), $adapter = new atoum\adapter()))
            ->then
                ->string($this->testedInstance->getName())->isEqualTo($name)
                ->string($this->testedInstance->getReportFormat())->isEqualTo('xml')
                ->object($this->testedInstance->getAdapter())->isIdenticalTo($adapter)
                ->object($this->testedInstance->getLocale())->isInstanceOf(atoum\locale::class)
                ->object($this->testedInstance->getIncluder())->isInstanceOf(atoum\includer::class)
                ->object($this->testedInstance->getRunner())->isInstanceOf(atoum\runner::class)
                ->variable($this->testedInstance->getScoreFile())->isNull()
                ->array($this->testedInstance->getArguments())->isEmpty()
                ->array($this->testedInstance->getHelp())->isEqualTo(
                    [
                        [
                            ['-h', '--help'],
                            null,
                            'Display this help'
                        ],
                        [
                            ['-c', '--configurations'],
                            '<file>...',
                            'Use all configuration files <file>'
                        ],
                        [
                            ['-v', '--version'],
                            null,
                            'Display version'
                        ],
                        [
                            ['+verbose', '++verbose'],
                            null,
                            'Enable verbose mode'
                        ],
                        [
                            ['--init'],
                            '<path/to/directory>',
                            sprintf($this->testedInstance->getLocale()->_('Create configuration and bootstrap files in <path/to/directory> (Optional, default: %s)'), $this->testedInstance->getDirectory())
                        ],
                        [
                            ['-p', '--php'],
                            '<path/to/php/binary>',
                            'Path to PHP binary which must be used to run tests'
                        ],
                        [
                            ['-drt', '--default-report-title'],
                            '<string>',
                            'Define default report title with <string>'
                        ],
                        [
                            ['-sf', '--score-file'],
                            '<file>',
                            'Save score in file <file>'
                        ],
                        [
                            ['-mcn', '--max-children-number'],
                            '<integer>',
                            'Maximum number of sub-processes which will be run simultaneously'
                        ],
                        [
                            ['-ncc', '--no-code-coverage'],
                            null,
                            'Disable code coverage'
                        ],
                        [
                            ['-nccid', '--no-code-coverage-in-directories'],
                            '<directory>...',
                            'Disable code coverage in directories <directory>'
                        ],
                        [
                            ['-nccfns', '--no-code-coverage-for-namespaces'],
                            '<namespace>...',
                            'Disable code coverage for namespaces <namespace>'
                        ],
                        [
                            ['-nccfc', '--no-code-coverage-for-classes'],
                            '<class>...',
                            'Disable code coverage for classes <class>'
                        ],
                        [
                            ['-nccfm', '--no-code-coverage-for-methods'],
                            '<method>...',
                            'Disable code coverage for methods <method>'
                        ],
                        [
                            ['-ebpc', '--enable-branch-and-path-coverage'],
                            null,
                            'Enable branch and path coverage'
                        ],
                        [
                            ['-f', '--files'],
                            '<file>...',
                            'Execute all unit test files <file>'
                        ],
                        [
                            ['-d', '--directories'],
                            '<directory>...',
                            'Execute unit test files in all <directory>'
                        ],
                        [
                            ['-tfe', '--test-file-extensions'],
                            '<extension>...',
                            'Execute unit test files with one of extensions <extension>'
                        ],
                        [
                            ['-g', '--glob'],
                            '<pattern>...',
                            'Execute unit test files which match <pattern>'
                        ],
                        [
                            ['-t', '--tags'],
                            '<tag>...',
                            'Execute only unit test with tags <tag>'
                        ],
                        [
                            ['-m', '--methods'],
                            '<class::method>...',
                            'Execute all <class::method>, * may be used as wildcard for class name or method name'
                        ],
                        [
                            ['-ns', '--namespaces'],
                            '<namespace>...',
                            'Execute all classes in all namespaces <namespace>'
                        ],
                        [
                            ['-l', '--loop'],
                            null,
                            'Execute tests in an infinite loop'
                        ],
                        [
                            ['--test-it'],
                            null,
                            'Execute atoum unit tests'
                        ],
                        [
                            ['-ft', '--force-terminal'],
                            null,
                            'Force output as in terminal'
                        ],
                        [
                            ['-af', '--autoloader-file'],
                            '<file>',
                            'Include autoloader <file> before executing each test method'
                        ],
                        [
                            ['-bf', '--bootstrap-file'],
                            '<file>',
                            'Include bootstrap <file> before executing each test method'
                        ],
                        [
                            ['-ulr', '--use-light-report'],
                            null,
                            'Use "light" CLI report'
                        ],
                        [
                            ['-udr', '--use-dot-report'],
                            null,
                            'Use "dot" CLI report'
                        ],
                        [
                            ['-utr', '--use-tap-report'],
                            null,
                            'Use TAP report'
                        ],
                        [
                            ['--debug'],
                            null,
                            'Enable debug mode'
                        ],
                        [
                            ['-xc', '--xdebug-config'],
                            null,
                            'Set XDEBUG_CONFIG variable'
                        ],
                        [
                            ['-fivm', '--fail-if-void-methods'],
                            null,
                            'Make the test suite fail if there is at least one void test method'
                        ],
                        [
                            ['-fism', '--fail-if-skipped-methods'],
                            null,
                            'Make the test suite fail if there is at least one skipped test method'
                        ],
                        [
                            ['-fmt', '--format'],
                            '<xml|clover|html|treemap>',
                            'Coverage report format'
                        ],
                        [
                            ['-o', '--output'],
                            '<path/to/file/or/directory>',
                            'Coverage report output path'
                        ]
                    ]
                )
        ;
    }
}
