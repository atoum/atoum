<?php

namespace mageekguy\atoum\tests\units\scripts;

use mageekguy\atoum;
use mageekguy\atoum\cli;
use mageekguy\atoum\mock\stream;
use mageekguy\atoum\scripts\runner as testedClass;
use mageekguy\atoum\writer;
use mock\mageekguy\atoum as mock;

require_once __DIR__ . '/../../runner.php';

class runner extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\script\configurable::class);
    }

    public function testClassConstants()
    {
        $this
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
                ->object($runner->getAdapter())->isInstanceOf(atoum\adapter::class)
                ->object($runner->getLocale())->isInstanceOf(atoum\locale::class)
                ->object($runner->getIncluder())->isInstanceOf(atoum\includer::class)
                ->object($runner->getRunner())->isInstanceOf(atoum\runner::class)
                ->variable($runner->getScoreFile())->isNull()
                ->array($runner->getReports())->isEmpty()
                ->array($runner->getArguments())->isEmpty()
                ->array($runner->getHelp())->isEqualTo(
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
                            sprintf($runner->getLocale()->_('Create configuration and bootstrap files in <path/to/directory> (Optional, default: %s)'), $runner->getDirectory())
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
                        ]
                    ]
                )
            ->if($runner = new testedClass($name = uniqid(), $adapter = new atoum\adapter()))
            ->then
                ->string($runner->getName())->isEqualTo($name)
                ->object($runner->getAdapter())->isIdenticalTo($adapter)
                ->object($runner->getLocale())->isInstanceOf(atoum\locale::class)
                ->object($runner->getIncluder())->isInstanceOf(atoum\includer::class)
                ->object($runner->getRunner())->isInstanceOf(atoum\runner::class)
                ->variable($runner->getScoreFile())->isNull()
                ->array($runner->getArguments())->isEmpty()
                ->array($runner->getHelp())->isEqualTo(
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
                            sprintf($runner->getLocale()->_('Create configuration and bootstrap files in <path/to/directory> (Optional, default: %s)'), $runner->getDirectory())
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
                        ]
                    ]
                )
        ;
    }

    public function testSetArguments()
    {
        $this
            ->if($runner = new testedClass($name = uniqid()))
            ->then
                ->object($runner->setArguments([]))->isIdenticalTo($runner)
                ->array($runner->getArguments())->isEmpty()
                ->object($runner->setArguments($arguments = [uniqid(), uniqid(), uniqid()]))->isIdenticalTo($runner)
                ->array($runner->getArguments())->isEqualTo($arguments)
        ;
    }

    public function testUseConfigFile()
    {
        $this
            ->if($runner = new testedClass(uniqid()))
            ->and($runner->setLocale($locale = new \mock\mageekguy\atoum\locale()))
            ->then
                ->exception(function () use ($runner, & $file) {
                    $runner->useConfigFile($file = uniqid());
                })
                    ->isInstanceOf(atoum\includer\exception::class)
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

    public function testAddDefaultReport()
    {
        $this
            ->given(
                $adapter = new atoum\test\adapter(),
                $adapter->getenv = false
            )
            ->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid(), $adapter))
            ->then
                ->object($report = $runner->addDefaultReport())->isInstanceOf(atoum\reports\realtime\cli::class)
                ->array($report->getWriters())->isEqualTo([new atoum\writers\std\out()])
                ->adapter($adapter)
                    ->call('getenv')->withArguments('TRAVIS')->once
            ->given($adapter->getenv = true)
            ->then
                ->object($report = $runner->addDefaultReport())->isInstanceOf(atoum\reports\realtime\cli\travis::class)
                ->array($report->getWriters())->isEqualTo([new atoum\writers\std\out()])
                ->adapter($adapter)
                    ->call('getenv')->withArguments('TRAVIS')->twice
        ;
    }

    public function testAddReport()
    {
        $this
            ->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->then
                ->object($runner->addReport($report = new \mock\mageekguy\atoum\report()))->isIdenticalTo($runner)
                ->array($runner->getReports())->isEqualTo([$report])
                ->object($runner->addReport($otherReport = new \mock\mageekguy\atoum\report()))->isIdenticalTo($runner)
                ->array($runner->getReports())->isEqualTo([$report, $otherReport])
        ;
    }

    public function testSetReport()
    {
        $this
            ->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->then
                ->object($runner->setReport($report = new \mock\mageekguy\atoum\report()))->isIdenticalTo($runner)
                ->array($runner->getReports())->isEqualTo([$report])
                ->object($runner->setReport($otherReport = new \mock\mageekguy\atoum\report()))->isIdenticalTo($runner)
                ->array($runner->getReports())->isEqualTo([$otherReport])
            ->if($runner->addReport($report))
            ->then
                ->array($runner->getReports())->isEqualTo([$otherReport])
        ;
    }

    public function testSetNamespaces()
    {
        $this
            ->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->then
                ->object($runner->testNamespaces([]))->isIdenticalTo($runner)
                ->array($runner->getTestedNamespaces())->isEmpty()
                ->object($runner->testNamespaces(['foo', '\bar', 'foo\bar\\', '\this\is\a\namespace\\']))->isIdenticalTo($runner)
                ->array($runner->getTestedNamespaces())->isEqualTo(['foo', 'bar', 'foo\bar', 'this\is\a\namespace'])
        ;
    }

    public function testSetPhpPath()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->setPhpPath($phpPath = uniqid()))->isIdenticalTo($script)
                ->mock($runner)->call('setPhpPath')->withArguments($phpPath)->once()
        ;
    }

    public function testSetDefaultReportTitle()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->setDefaultReportTitle($reportTitle = uniqid()))->isIdenticalTo($script)
                ->mock($runner)->call('setDefaultReportTitle')->withArguments($reportTitle)->once()
        ;
    }

    public function testSetMaxChildrenNumber()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->setMaxChildrenNumber($childrenNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($script)
                ->mock($runner)->call('setMaxChildrenNumber')->withArguments($childrenNumber)->once()
        ;
    }

    public function testDisableCodeCoverage()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->disableCodeCoverage($childrenNumber = rand(1, PHP_INT_MAX)))->isIdenticalTo($script)
                ->mock($runner)->call('disableCodeCoverage')->withoutAnyArgument()->once()
        ;
    }

    public function testExcludeNamespacesFromCoverage()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->getCoverage = $coverage = new \mock\mageekguy\atoum\score\coverage())
            ->then
                ->object($script->excludeNamespacesFromCoverage(['foo', 'bar']))->isIdenticalTo($script)
                ->mock($coverage)->call('excludeNamespace')
                    ->withArguments('foo')->once()
                    ->withArguments('bar')->once()
        ;
    }

    public function testExcludeDirectoriesFromCoverage()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->getCoverage = $coverage = new \mock\mageekguy\atoum\score\coverage())
            ->then
                ->object($script->excludeDirectoriesFromCoverage(['foo', 'bar']))->isIdenticalTo($script)
                ->mock($coverage)->call('excludeDirectory')
                    ->withArguments('foo')->once()
                    ->withArguments('bar')->once()
        ;
    }

    public function testExcludeClassesFromCoverage()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->getCoverage = $coverage = new \mock\mageekguy\atoum\score\coverage())
            ->then
                ->object($script->excludeClassesFromCoverage(['foo', 'bar']))->isIdenticalTo($script)
                ->mock($coverage)->call('excludeClass')
                    ->withArguments('foo')->once()
                    ->withArguments('bar')->once()
        ;
    }

    public function testAddTest()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->addTest($testPath = uniqid()))->isIdenticalTo($script)
                ->mock($runner)->call('addTest')->withArguments($testPath)->once()
        ;
    }

    public function testAddTests()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->addTests([$testPath1 = uniqid(), $testPath2 = uniqid()]))->isIdenticalTo($script)
                ->mock($runner)
                    ->call('addTest')
                        ->withArguments($testPath1)->once()
                        ->withArguments($testPath2)->once()
        ;
    }

    public function testAddTestsFromDirectory()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->addTestsFromDirectory->doesNothing())
            ->then
                ->object($script->addTestsFromDirectory($directory = uniqid()))->isIdenticalTo($script)
                ->mock($runner)->call('addTestsFromDirectory')->withArguments($directory)->once()
        ;
    }

    public function testAddTestsFromDirectories()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->addTestsFromDirectory->doesNothing())
            ->then
                ->object($script->addTestsFromDirectories([$directory1 = uniqid(), $directory2 = uniqid()]))->isIdenticalTo($script)
                ->mock($runner)
                    ->call('addTestsFromDirectory')
                        ->withArguments($directory1)->once()
                        ->withArguments($directory2)->once()
        ;
    }

    public function testAddTestsFromPattern()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->addTestsFromPattern->doesNothing())
            ->then
                ->object($script->addTestsFromPattern($pattern = uniqid()))->isIdenticalTo($script)
                ->mock($runner)->call('addTestsFromPattern')->withArguments($pattern)->once()
        ;
    }

    public function testAddTestsFromPatterns()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->addTestsFromPattern->doesNothing())
            ->then
                ->object($script->addTestsFromPatterns([$pattern1 = uniqid(), $pattern2 = uniqid()]))->isIdenticalTo($script)
                ->mock($runner)
                    ->call('addTestsFromPattern')
                        ->withArguments($pattern1)->once()
                        ->withArguments($pattern2)->once()
        ;
    }

    public function testAcceptTestFileExtensions()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->acceptTestFileExtensions->doesNothing())
            ->then
                ->object($script->acceptTestFileExtensions($testFileExtensions = [uniqid(), uniqid()]))->isIdenticalTo($script)
                ->mock($runner)->call('acceptTestFileExtensions')->withArguments($testFileExtensions)->once()
        ;
    }

    public function testSetBootstrapFile()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->setBootstrapFile->doesNothing())
            ->then
                ->object($script->setBootstrapFile($bootstrapFile = uniqid()))->isIdenticalTo($script)
                ->mock($runner)->call('setBootstrapFile')->withArguments($bootstrapFile)->once()
        ;
    }

    public function testSetXdebugConfig()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->setXdebugConfig->doesNothing())
            ->then
                ->object($script->setXdebugConfig($xdebugConfig = uniqid()))->isIdenticalTo($script)
                ->mock($runner)->call('setXdebugConfig')->withArguments($xdebugConfig)->once()
        ;
    }

    public function testFailIfVoidMethods()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->failIfVoidMethods())->isIdenticalTo($script)
                ->mock($runner)->call('failIfVoidMethods')->once()
        ;
    }

    public function testDoNotFailIfVoidMethods()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->doNotFailIfVoidMethods())->isIdenticalTo($script)
                ->mock($runner)->call('doNotFailIfVoidMethods')->once()
        ;
    }

    public function testShouldFailIfVoidMethods()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->boolean($script->shouldFailIfVoidMethods())->isFalse()
                ->mock($runner)->call('shouldFailIfVoidMethods')->once()
            ->if($this->calling($runner)->shouldFailIfVoidMethods = true)
            ->then
                ->boolean($script->shouldFailIfVoidMethods())->isTrue()
            ->if($this->calling($runner)->shouldFailIfVoidMethods = false)
            ->then
                ->boolean($script->shouldFailIfVoidMethods())->isFalse()
        ;
    }

    public function testFailIfSkippedMethods()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->failIfSkippedMethods())->isIdenticalTo($script)
                ->mock($runner)->call('failIfSkippedMethods')->once()
        ;
    }

    public function testDoNotFailIfSkippedMethods()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->object($script->doNotFailIfSkippedMethods())->isIdenticalTo($script)
                ->mock($runner)->call('doNotFailIfSkippedMethods')->once()
        ;
    }

    public function testShouldFailIfSkippedMethods()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->then
                ->boolean($script->shouldFailIfSkippedMethods())->isFalse()
                ->mock($runner)->call('shouldFailIfSkippedMethods')->once()
            ->if($this->calling($runner)->shouldFailIfSkippedMethods = true)
            ->then
                ->boolean($script->shouldFailIfSkippedMethods())->isTrue()
            ->if($this->calling($runner)->shouldFailIfSkippedMethods = false)
            ->then
                ->boolean($script->shouldFailIfSkippedMethods())->isFalse()
        ;
    }

    public function testEnableDebugMode()
    {
        $this
            ->if($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->and($script->setRunner($runner = new \mock\mageekguy\atoum\runner()))
            ->and($this->calling($runner)->enableDebugMode->doesNothing())
            ->then
                ->object($script->enableDebugMode())->isIdenticalTo($script)
                ->mock($runner)->call('enableDebugMode')->withoutAnyArgument()->once()
        ;
    }

    public function testAddDefaultArguments()
    {
        $this
            ->if($runner = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
            ->then
                ->object($runner->addDefaultArguments($arg1 = uniqid()))->isInstanceOf($runner)
                ->boolean($runner->hasDefaultArguments())->isTrue()
                ->array($runner->getDefaultArguments())->isEqualTo([$arg1])
                ->object($runner->addDefaultArguments($arg2 = uniqid(), $arg3 = uniqid()))->isInstanceOf($runner)
                ->boolean($runner->hasDefaultArguments())->isTrue()
                ->array($runner->getDefaultArguments())->isEqualTo([$arg1, $arg2, $arg3])
        ;
    }

    public function testInit()
    {
        $this
            ->given($runner = new testedClass(__FILE__))
            ->and($runner->setAdapter($adapter = new atoum\test\adapter()))
            ->and($runner->setInfoWriter($outputWriter = new \mock\mageekguy\atoum\writers\std\out()))
            ->and($runner->setPrompt($prompt = new \mock\mageekguy\atoum\script\prompt()))
            ->and($adapter->copy = true)
            ->and($adapter->file_exists = false)
            ->and($this->calling($outputWriter)->write = function () {
            })
            ->then
                ->object($runner->init())->isIdenticalTo($runner)
                ->mock($prompt)
                    ->call('ask')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in ' . $runner->getDirectory() . ', type \'Y\' to overwrite it...')->never()
                ->mock($outputWriter)
                    ->call('write')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in ' . $runner->getDirectory())->once()
                        ->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in ' . $runner->getDirectory())->once()
                ->adapter($adapter)
                    ->call('copy')
                        ->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', getcwd() . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->once()
                        ->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', getcwd() . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->once()
            ->if($this->resetAdapter($adapter))
            ->and($this->resetMock($outputWriter))
            ->then
                ->object($runner->init($directory = uniqid()))->isIdenticalTo($runner)
                ->mock($prompt)
                    ->call('ask')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in ' . $directory . ', type \'Y\' to overwrite it...')->never()
                ->mock($outputWriter)
                    ->call('write')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in ' . $directory . DIRECTORY_SEPARATOR)->once()
                        ->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in ' . $directory . DIRECTORY_SEPARATOR)->once()
                ->adapter($adapter)
                    ->call('copy')
                        ->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', $directory . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->once()
                        ->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', $directory . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->once()
            ->if($this->resetAdapter($adapter))
            ->and($this->resetMock($outputWriter))
            ->and($adapter->file_exists = true)
            ->and($this->calling($prompt)->ask = 'Y')
            ->then
                ->object($runner->init())->isIdenticalTo($runner)
                ->mock($prompt)
                    ->call('ask')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in ' . $runner->getDirectory() . ', type \'Y\' to overwrite it...')->once()
                        ->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' already exists in ' . $runner->getDirectory() . ', type \'Y\' to overwrite it...')->once()
                ->mock($outputWriter)
                    ->call('write')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in ' . $runner->getDirectory())->once()
                        ->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in ' . $runner->getDirectory())->once()
                ->adapter($adapter)
                    ->call('copy')
                        ->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', getcwd() . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->once()
                        ->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', getcwd() . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->once()
            ->if($this->resetAdapter($adapter))
            ->and($this->resetMock($outputWriter))
            ->and($this->resetMock($prompt))
            ->and($adapter->file_exists = true)
            ->and($this->calling($prompt)->ask = 'y')
            ->then
                ->object($runner->init())->isIdenticalTo($runner)
                ->mock($prompt)
                    ->call('ask')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in ' . $runner->getDirectory() . ', type \'Y\' to overwrite it...')->once()
                        ->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' already exists in ' . $runner->getDirectory() . ', type \'Y\' to overwrite it...')->once()
                ->mock($outputWriter)
                    ->call('write')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in ' . $runner->getDirectory())->never()
                        ->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in ' . $runner->getDirectory())->never()
                ->adapter($adapter)
                    ->call('copy')
                        ->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', getcwd() . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->never()
                        ->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', getcwd() . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->never()
            ->if($this->resetAdapter($adapter))
            ->and($this->resetMock($outputWriter))
            ->and($this->resetMock($prompt))
            ->and($adapter->file_exists = true)
            ->and($this->calling($prompt)->ask = uniqid())
            ->then
                ->object($runner->init())->isIdenticalTo($runner)
                ->mock($prompt)
                    ->call('ask')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' already exists in ' . $runner->getDirectory() . ', type \'Y\' to overwrite it...')->once()
                        ->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' already exists in ' . $runner->getDirectory() . ', type \'Y\' to overwrite it...')->once()
                ->mock($outputWriter)
                    ->call('write')
                        ->withArguments('Default configuration file \'' . testedClass::defaultConfigFile . '\' was successfully created in ' . $runner->getDirectory() . PHP_EOL)->never()
                        ->withArguments('Default bootstrap file \'' . testedClass::defaultBootstrapFile . '\' was successfully created in ' . $runner->getDirectory() . PHP_EOL)->never()
                ->adapter($adapter)
                    ->call('copy')
                        ->withArguments(atoum\directory . '/resources/configurations/runner/atoum.php.dist', __DIR__ . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile)->never()
                        ->withArguments(atoum\directory . '/resources/configurations/runner/bootstrap.php.dist', __DIR__ . DIRECTORY_SEPARATOR . testedClass::defaultBootstrapFile)->never()
            ->if($this->calling($prompt)->ask = 'Y')
            ->and($adapter->copy = false)
            ->then
                ->exception(function () use ($runner) {
                    $runner->init();
                })
                    ->isInstanceOf(atoum\exceptions\runtime::class)
                    ->hasMessage('Unable to write \'' . atoum\directory . '/resources/configurations/runner/atoum.php.dist\' to \'' . getcwd() . DIRECTORY_SEPARATOR . testedClass::defaultConfigFile . '\'')
        ;
    }

    public function testSetInfoWriter()
    {
        $this
            ->given($runner = new testedClass(uniqid()))
            ->then
                ->object($runner->setInfoWriter($errorWriter = new atoum\writers\std\err()))->isIdenticalTo($runner)
                ->object($runner->getInfoWriter())->isIdenticalTo($errorWriter)
            ->given(
                $defaultInfoWriter = new atoum\writers\std\out(),
                $defaultInfoWriter
                    ->addDecorator(new writer\decorators\rtrim())
                    ->addDecorator(new writer\decorators\eol())
                    ->addDecorator(new atoum\cli\clear())
                    ->addDecorator(new cli\colorizer('0;32'))
            )
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
            ->given(
                $colorizer = new cli\colorizer('0;31'),
                $colorizer->setPattern('/^([^:]+:)/'),
                $defaultErrorWriter = new atoum\writers\std\err(),
                $defaultErrorWriter
                    ->addDecorator(new writer\decorators\trim())
                    ->addDecorator(new writer\decorators\prompt($runner->getLocale()->_('Error: ')))
                    ->addDecorator(new writer\decorators\eol())
                    ->addDecorator(new atoum\cli\clear())
                    ->addDecorator($colorizer)
            )
            ->then
                ->object($runner->setErrorWriter())->isIdenticalTo($runner)
                ->object($runner->getErrorWriter())->isEqualTo($defaultErrorWriter)
        ;
    }

    public function testHelp()
    {
        $this
            ->if($argumentsParser = new mock\script\arguments\parser())
            ->and($this->calling($argumentsParser)->addHandler = function () {
            })
            ->and($locale = new mock\locale())
            ->and($this->calling($locale)->_ = function ($string) {
                return vsprintf($string, array_slice(func_get_args(), 1));
            })
            ->and($helpWriter = new mock\writers\std\out())
            ->and($this->calling($helpWriter)->write = function () {
            })
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
            ->and($this->calling($locale)->_ = function ($string) {
                return $string;
            })
            ->and($helpWriter = new mock\writers\std\out())
            ->and($this->calling($helpWriter)->write = function () {
            })
            ->and($errorWriter = new mock\writers\std\err())
            ->and($this->calling($errorWriter)->clear = $errorWriter)
            ->and($this->calling($errorWriter)->write = $errorWriter)
            ->and($runner = new mock\runner())
            ->and($this->calling($runner)->getTestPaths = [])
            ->and($this->calling($runner)->getDeclaredTestClasses = [])
            ->and($this->calling($runner)->run = function () {
            })
            ->and($script = new testedClass($name = uniqid()))
            ->and($script->setLocale($locale))
            ->and($script->setHelpWriter($helpWriter))
            ->and($script->setErrorWriter($errorWriter))
            ->and($script->setRunner($runner))
            ->then
                ->object($script->run())->isIdenticalTo($script)
                ->mock($locale)
                    ->call('_')
                        ->withArguments('No test found')->once()
                ->mock($errorWriter)->call('write')->withArguments('No test found')->once()
        ;
    }

    public function testAutorun()
    {
        $this
            ->if($script = new testedClass(uniqid()))
            ->and($script->setAdapter($adapter = new atoum\test\adapter()))
            ->and($adapter->realpath = function ($path) {
                return $path;
            })
            ->when(function () {
                if (isset($_SERVER['argv']) === true) {
                    unset($_SERVER['argv']);
                }
            })
            ->then
                ->boolean($script->autorun())->isTrue()
            ->if($_SERVER['argv'] = [])
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
