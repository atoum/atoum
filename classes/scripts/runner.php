<?php

namespace mageekguy\atoum\scripts;

require_once __DIR__ . '/../../constants.php';

use mageekguy\atoum;
use mageekguy\atoum\cli;
use mageekguy\atoum\exceptions;
use mageekguy\atoum\php;
use mageekguy\atoum\writers;

class runner extends atoum\script\configurable
{
    const defaultConfigFile = '.atoum.php';
    const defaultBootstrapFile = '.bootstrap.atoum.php';
    const defaultAutoloaderFile = '.autoloader.atoum.php';
    const defaultComposerAutoloaderFile = 'vendor/autoload.php';

    protected $runner = null;
    protected $configuratorFactory = null;
    protected $defaultReportFactory = null;
    protected $scoreFile = null;
    protected $arguments = [];
    protected $defaultArguments = [];
    protected $namespaces = [];
    protected $tags = [];
    protected $methods = [];
    protected $loop = false;
    protected $looper;

    protected static $autorunner = true;
    protected static $runnerFile = null;
    protected static $configurationCallables = [];

    public function __construct($name, atoum\adapter $adapter = null, atoum\scripts\runner\looper $looper = null)
    {
        parent::__construct($name, $adapter);

        $this
            ->setRunner()
            ->setConfiguratorFactory()
            ->setDefaultReportFactory()
            ->setLooper($looper)
            ->setDefaultAutoloaderFiles()
        ;
    }

    public function setInfoWriter(atoum\writer $writer = null)
    {
        parent::setInfoWriter($writer);

        if ($writer === null) {
            $this->infoWriter->addDecorator(new cli\colorizer('0;32'));
        }

        return $this;
    }

    public function setWarningWriter(atoum\writer $writer = null)
    {
        if ($writer === null) {
            $writer = new writers\std\err();

            $colorizer = new cli\colorizer('0;33');
            $colorizer->setPattern('/^([^:]+:)/');

            $writer->addDecorator($colorizer);
        }

        parent::setWarningWriter($writer);

        return $this;
    }

    public function setErrorWriter(atoum\writer $writer = null)
    {
        parent::setErrorWriter($writer);

        if ($writer === null) {
            $colorizer = new cli\colorizer('0;31');
            $colorizer->setPattern('/^([^:]+:)/');

            $this->errorWriter->addDecorator($colorizer);
        }

        return $this;
    }

    public function getResourcesDirectory()
    {
        return atoum\directory . '/resources';
    }

    public function setRunner(atoum\runner $runner = null)
    {
        $this->runner = $runner ?: new atoum\runner();

        return $this->setArgumentHandlers();
    }

    public function getRunner()
    {
        return $this->runner;
    }

    public function setConfiguratorFactory(\closure $factory = null)
    {
        $this->configuratorFactory = $factory ?: function ($test) {
            return new atoum\configurator($test);
        };

        return $this;
    }

    public function getConfiguratorFactory()
    {
        return $this->configuratorFactory;
    }

    public function setDefaultReportFactory(\closure $factory = null)
    {
        $this->defaultReportFactory = $factory ?: function ($script) {
            $report = new atoum\reports\realtime\cli();

            if ($this->getAdapter()->getenv('TRAVIS') !== false) {
                $report = new atoum\reports\realtime\cli\travis();
            }

            $report->addWriter($script->getOutputWriter());

            return $report;
        };

        return $this;
    }

    public function getDefaultReportFactory()
    {
        return $this->defaultReportFactory;
    }

    public function setLooper(atoum\scripts\runner\looper $looper = null)
    {
        $this->looper = $looper ?: new atoum\scripts\runner\loopers\prompt($this->prompt, $this->outputWriter, $this->cli, $this->locale);

        return $this;
    }

    public function getLooper()
    {
        return $this->looper;
    }

    public function autorun()
    {
        return (isset($_SERVER['argv']) === false || isset($_SERVER['argv'][0]) === false || $this->adapter->realpath($_SERVER['argv'][0]) !== $this->getName());
    }

    public function setScoreFile($path)
    {
        $this->scoreFile = (string) $path;

        return $this;
    }

    public function getScoreFile()
    {
        return $this->scoreFile;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function addDefaultArguments(...$arguments)
    {
        $this->defaultArguments = array_merge($this->defaultArguments, $arguments);

        return $this;
    }

    public function hasDefaultArguments()
    {
        return (count($this->defaultArguments) > 0);
    }

    public function getDefaultArguments()
    {
        return $this->defaultArguments;
    }

    public function run(array $arguments = [])
    {
        # Default bootstrap file MUST be included here because some arguments on the command line can include some tests which depends of this file.
        # So, this file must be included BEFORE argument parsing which is done in script::run().
        # Default bootstrap file can be overrided in a default config file included in script\configurable::run() which extends script::run().
        # So, if a bootstrap file is defined in a default config file, it will be available when arguments on CLI will be parsed
        $this->setDefaultBootstrapFiles();

        if ($this->autorun() === true && count($this->runner->getDeclaredTestClasses()) > 0) {
            $this->runner->canNotAddTest();
        }

        try {
            parent::run($arguments ?: $this->getArguments());
        } catch (atoum\exception $exception) {
            $this->writeError($exception->getMessage());

            exit(2);
        }

        return $this;
    }

    public function version()
    {
        $this
            ->writeInfo($this->locale->_('atoum version %s by %s (%s)', atoum\version, atoum\author, atoum\directory))
            ->stopRun()
        ;

        return $this;
    }

    public function useConfigFile($path)
    {
        $script = call_user_func($this->configuratorFactory, $this);
        $runner = $this->runner;

        return $this->includeConfigFile($path, function ($path) use ($script, $runner) {
            include_once($path);
        });
    }

    public function useConfigurationCallable(\closure $callback)
    {
        $script = call_user_func($this->configuratorFactory, $this);
        $runner = $this->runner;
        $errors = [];

        $this->adapter->set_error_handler(function ($error, $message, $file, $line, $context) use (&$errors) {
            foreach (array_reverse(debug_backtrace()) as $trace) {
                if (isset($trace['file']) === true && $trace['file'] === __DIR__) {
                    $file = __DIR__;
                    $line = $trace['line'];

                    break;
                }
            }

            $errors[] = [$error, $message, $file, $line, $context];
        });

        $callback($script, $runner);

        $this->adapter->restore_error_handler();

        if (count($errors) > 0) {
            list($error, $message, $file, $line, $context) = $errors[0];

            throw new exceptions\runtime('Unable to configure runner. ' . atoum\asserters\error::getAsString($error) . ': ' . $message . ' in ' . $file . ' on line ' . $line);
        }

        return $this;
    }

    public function testIt()
    {
        $this->runner
            ->addTestsFromDirectory(atoum\directory . '/tests/units/classes')
            ->addTestsFromDirectory(atoum\directory . '/tests/units/resources')
            ->addTestsFromDirectory(atoum\directory . '/tests/functionals/classes')
        ;

        return $this;
    }

    public function enableLoopMode()
    {
        if ($this->loop !== null) {
            $this->loop = true;
        }

        return $this;
    }

    public function disableLoopMode()
    {
        $this->loop = null;

        return $this;
    }

    public function testNamespaces(array $namespaces)
    {
        foreach ($namespaces as $namespace) {
            $this->namespaces[] = trim($namespace, '\\');
        }

        return $this;
    }

    public function getTestedNamespaces()
    {
        return $this->namespaces;
    }

    public function resetTestedNamespaces()
    {
        $this->namespaces = [];

        return $this;
    }

    public function testTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    public function testMethod($class, $method)
    {
        $this->methods[$class][] = $method;

        return $this;
    }

    public function resetTestMethods()
    {
        $this->methods = [];

        return $this;
    }

    public function addDefaultReport()
    {
        $report = call_user_func($this->defaultReportFactory, $this);

        $this->addReport($report);

        return $report;
    }

    public function addReport(atoum\report $report)
    {
        $this->runner->addReport($report);

        return $this;
    }

    public function setReport(atoum\report $report)
    {
        $this->runner->setReport($report);

        return $this;
    }

    public function getReports()
    {
        return $this->runner->getReports();
    }

    public function setPhpPath($phpPath)
    {
        $this->runner->setPhpPath($phpPath);

        return $this;
    }

    public function setDefaultReportTitle($reportTitle)
    {
        $this->runner->setDefaultReportTitle($reportTitle);

        return $this;
    }

    public function setMaxChildrenNumber($childrenNumber)
    {
        $this->runner->setMaxChildrenNumber($childrenNumber);

        return $this;
    }

    public function disableCodeCoverage()
    {
        $this->runner->disableCodeCoverage();

        return $this;
    }

    public function enableBranchesAndPathsCoverage()
    {
        $this->runner->enableBranchesAndPathsCoverage();

        return $this;
    }

    public function excludeNamespacesFromCoverage(array $namespaces)
    {
        $coverage = $this->runner->getCoverage();

        foreach ($namespaces as $namespace) {
            $coverage->excludeNamespace($namespace);
        }

        return $this;
    }

    public function resetExcludedNamespacesFromCoverage()
    {
        $this->runner->getCoverage()->resetExcludedNamespaces();

        return $this;
    }

    public function excludeDirectoriesFromCoverage(array $directories)
    {
        $coverage = $this->runner->getCoverage();

        foreach ($directories as $directory) {
            $coverage->excludeDirectory($directory);
        }

        return $this;
    }

    public function resetExcludedDirectoriesFromCoverage()
    {
        $this->runner->getCoverage()->resetExcludedDirectories();

        return $this;
    }

    public function excludeClassesFromCoverage(array $classes)
    {
        $coverage = $this->runner->getCoverage();

        foreach ($classes as $class) {
            $coverage->excludeClass($class);
        }

        return $this;
    }

    public function resetExcludedClassesFromCoverage()
    {
        $this->runner->getCoverage()->resetExcludedClasses();

        return $this;
    }

    public function excludeMethodsFromCoverage(array $methods)
    {
        $coverage = $this->runner->getCoverage();

        foreach ($methods as $method) {
            $coverage->excludeMethod($method);
        }

        return $this;
    }

    public function resetExcludedMethodsFromCoverage()
    {
        $this->runner->getCoverage()->resetExcludedMethods();

        return $this;
    }

    public function addTest($testPath)
    {
        $this->runner->addTest($testPath);

        return $this;
    }

    public function addTests(array $testPaths)
    {
        foreach ($testPaths as $testPath) {
            $this->addTest($testPath);
        }

        return $this;
    }

    public function addTestsFromDirectory($directory)
    {
        $this->runner->addTestsFromDirectory($directory);

        return $this;
    }

    public function addTestsFromDirectories(array $directories)
    {
        foreach ($directories as $directory) {
            $this->addTestsFromDirectory($directory);
        }

        return $this;
    }

    public function addTestsFromPattern($pattern)
    {
        $this->runner->addTestsFromPattern($pattern);

        return $this;
    }

    public function addTestsFromPatterns(array $patterns)
    {
        foreach ($patterns as $pattern) {
            $this->addTestsFromPattern($pattern);
        }

        return $this;
    }

    public function acceptTestFileExtensions(array $testFileExtensions)
    {
        $this->runner->acceptTestFileExtensions($testFileExtensions);

        return $this;
    }

    public function setBootstrapFile($bootstrapFile)
    {
        $this->runner->setBootstrapFile($bootstrapFile);

        return $this;
    }

    public function setAutoloaderFile($autoloaderFile)
    {
        $this->runner->setAutoloaderFile($autoloaderFile);

        return $this;
    }

    public function enableDebugMode()
    {
        $this->runner->enableDebugMode();

        return $this;
    }

    public function setXdebugConfig($xdebugConfig)
    {
        $this->runner->setXdebugConfig($xdebugConfig);

        return $this;
    }

    public function doNotfailIfVoidMethods()
    {
        $this->runner->doNotfailIfVoidMethods();

        return $this;
    }

    public function failIfVoidMethods()
    {
        $this->runner->failIfVoidMethods();

        return $this;
    }

    public function shouldFailIfVoidMethods()
    {
        return $this->runner->shouldFailIfVoidMethods();
    }

    public function doNotfailIfSkippedMethods()
    {
        $this->runner->doNotfailIfSkippedMethods();

        return $this;
    }

    public function failIfSkippedMethods()
    {
        $this->runner->failIfSkippedMethods();

        return $this;
    }

    public function shouldFailIfSkippedMethods()
    {
        return $this->runner->shouldFailIfSkippedMethods();
    }

    public function init($directory = null)
    {
        $resourceDirectory = static::getResourcesDirectory();
        $currentDirectory = $this->getDirectory();

        if ($directory !== null) {
            $currentDirectory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        $defaultConfigFile = $currentDirectory . static::defaultConfigFile;

        if ($this->adapter->file_exists($defaultConfigFile) === false || $this->prompt($this->locale->_('Default configuration file \'' . static::defaultConfigFile . '\' already exists in ' . $currentDirectory . ', type \'Y\' to overwrite it...')) === 'Y') {
            $this
                ->copy($resourceDirectory . '/configurations/runner/atoum.php.dist', $defaultConfigFile)
                ->writeInfo($this->locale->_('Default configuration file \'' . static::defaultConfigFile . '\' was successfully created in ' . $currentDirectory))
            ;
        }

        $bootstrapFile = $currentDirectory . static::defaultBootstrapFile;

        if ($this->adapter->file_exists($bootstrapFile) == false || $this->prompt($this->locale->_('Default bootstrap file \'' . static::defaultBootstrapFile . '\' already exists in ' . $currentDirectory . ', type \'Y\' to overwrite it...')) === 'Y') {
            $this
                ->copy($resourceDirectory . '/configurations/runner/bootstrap.php.dist', $bootstrapFile)
                ->writeInfo($this->locale->_('Default bootstrap file \'' . static::defaultBootstrapFile . '\' was successfully created in ' . $currentDirectory))
            ;
        }

        return $this->stopRun();
    }

    public function setDefaultBootstrapFiles($startDirectory = null)
    {
        foreach (self::getSubDirectoryPath($startDirectory ?: $this->getDirectory()) as $directory) {
            $defaultBootstrapFile = $directory . static::defaultBootstrapFile;

            if ($this->adapter->is_file($defaultBootstrapFile) === true) {
                $this->setBootstrapFile($defaultBootstrapFile);

                break;
            }
        }

        return $this;
    }

    public function setDefaultAutoloaderFiles($startDirectory = null)
    {
        foreach (self::getSubDirectoryPath($startDirectory ?: $this->getDirectory()) as $directory) {
            $defaultAutoloaderFile = $directory . static::defaultAutoloaderFile;

            if ($this->adapter->is_file($defaultAutoloaderFile) === true) {
                $this->setAutoloaderFile($defaultAutoloaderFile);

                return $this;
            }
        }

        foreach (self::getSubDirectoryPath($startDirectory ?: $this->getDirectory()) as $directory) {
            $composerAutoloaderFile = $directory . static::defaultComposerAutoloaderFile;

            if ($this->adapter->is_file($composerAutoloaderFile) === true) {
                $this->setAutoloaderFile($composerAutoloaderFile);

                break;
            }
        }

        return $this;
    }

    public static function autorunMustBeEnabled()
    {
        return (static::$autorunner === true);
    }

    public static function enableAutorun($name)
    {
        static $autorunIsRegistered = false;

        if (static::$autorunner instanceof static) {
            throw new exceptions\runtime('Unable to autorun \'' . $name . '\' because \'' . static::$autorunner->getName() . '\' is already set as autorunner');
        }

        if ($autorunIsRegistered === false) {
            $autorunner = & static::$autorunner;
            $calledClass = get_called_class();

            register_shutdown_function(
                function () use (& $autorunner, $calledClass) {
                    if ($autorunner instanceof $calledClass) {
                        set_error_handler(function ($error, $message, $file, $line) use ($autorunner) {
                            $errorReporting = error_reporting();

                            if ($errorReporting !== 0 && $errorReporting & $error) {
                                $autorunner->writeError($message . ' in ' . $file . ' at line ' . $line, $error);

                                exit(3);
                            }
                        });

                        try {
                            $score = $autorunner->run()->getRunner()->getScore();
                            $isSuccess = $score->getFailNumber() <= 0 && $score->getErrorNumber() <= 0 && $score->getExceptionNumber() <= 0 && $score->getUncompletedMethodNumber() <= 0;

                            if ($autorunner->shouldFailIfVoidMethods() && $score->getVoidMethodNumber() > 0) {
                                $isSuccess = false;
                            }

                            if ($autorunner->shouldFailIfSkippedMethods() && $score->getSkippedMethodNumber() > 0) {
                                $isSuccess = false;
                            }

                            exit($isSuccess ? 0 : 1);
                        } catch (\exception $exception) {
                            $autorunner->writeError($exception->getMessage());

                            exit(2);
                        }
                    }
                }
            );

            $autorunIsRegistered = true;
        }

        static::$autorunner = new static($name);

        foreach (static::$configurationCallables as $callable) {
            try {
                static::$autorunner->useConfigurationCallable($callable);
            } catch (\exception $exception) {
                static::$autorunner->writeError($exception->getMessage());

                static::$autorunner = null;

                exit($exception->getCode());
            }
        }

        return static::$autorunner;
    }

    public static function disableAutorun()
    {
        static::$autorunner = false;
    }

    public static function addConfigurationCallable(callable $callable)
    {
        static::$configurationCallables[] = $callable;
    }

    protected function setArgumentHandlers()
    {
        parent::setArgumentHandlers()
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) !== 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->version();
                    },
                    ['-v', '--version'],
                    null,
                    $this->locale->_('Display version')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) !== 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->resetVerbosityLevel();

                        $verbosityLevel = substr_count($argument, '+');

                        while ($verbosityLevel--) {
                            $script->increaseVerbosityLevel();
                        }
                    },
                    ['+verbose', '++verbose'],
                    null,
                    $this->locale->_('Enable verbose mode')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) === 0) {
                            $values = [getcwd()];
                        }

                        $script->init(current($values));
                    },
                    ['--init'],
                    '<path/to/directory>',
                    $this->locale->_('Create configuration and bootstrap files in <path/to/directory> (Optional, default: %s)', $this->getDirectory())
                )
            ->addArgumentHandler(
                    function ($script, $argument, $path) {
                        if (count($path) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->setPhpPath(reset($path));
                    },
                    ['-p', '--php'],
                    '<path/to/php/binary>',
                    $this->locale->_('Path to PHP binary which must be used to run tests')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $defaultReportTitle) {
                        if (count($defaultReportTitle) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->setDefaultReportTitle(reset($defaultReportTitle));
                    },
                    ['-drt', '--default-report-title'],
                    '<string>',
                    $this->locale->_('Define default report title with <string>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $file) {
                        if (count($file) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->setScoreFile(reset($file));
                    },
                    ['-sf', '--score-file'],
                    '<file>',
                    $this->locale->_('Save score in file <file>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $maxChildrenNumber) {
                        if (count($maxChildrenNumber) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->setMaxChildrenNumber(reset($maxChildrenNumber));
                    },
                    ['-mcn', '--max-children-number'],
                    '<integer>',
                    $this->locale->_('Maximum number of sub-processes which will be run simultaneously')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $empty) {
                        if ($empty) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->disableCodeCoverage();
                    },
                    ['-ncc', '--no-code-coverage'],
                    null,
                    $this->locale->_('Disable code coverage')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $directories) {
                        if (count($directories) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script
                            ->resetExcludedDirectoriesFromCoverage()
                            ->excludeDirectoriesFromCoverage($directories)
                        ;
                    },
                    ['-nccid', '--no-code-coverage-in-directories'],
                    '<directory>...',
                    $this->locale->_('Disable code coverage in directories <directory>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $namespaces) {
                        if (count($namespaces) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script
                            ->resetExcludedNamespacesFromCoverage()
                            ->excludeNamespacesFromCoverage($namespaces)
                        ;
                    },
                    ['-nccfns', '--no-code-coverage-for-namespaces'],
                    '<namespace>...',
                    $this->locale->_('Disable code coverage for namespaces <namespace>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $classes) {
                        if (count($classes) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script
                            ->resetExcludedClassesFromCoverage()
                            ->excludeClassesFromCoverage($classes)
                        ;
                    },
                    ['-nccfc', '--no-code-coverage-for-classes'],
                    '<class>...',
                    $this->locale->_('Disable code coverage for classes <class>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $classes) {
                        if (count($classes) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script
                            ->resetExcludedMethodsFromCoverage()
                            ->excludeMethodsFromCoverage($classes)
                        ;
                    },
                    ['-nccfm', '--no-code-coverage-for-methods'],
                    '<method>...',
                    $this->locale->_('Disable code coverage for methods <method>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $empty) {
                        if ($empty) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->enableBranchesAndPathsCoverage();
                    },
                    ['-ebpc', '--enable-branch-and-path-coverage'],
                    null,
                    $this->locale->_('Enable branch and path coverage')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $files) {
                        if (count($files) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->addTests($files);
                    },
                    ['-f', '--files'],
                    '<file>...',
                    $this->locale->_('Execute all unit test files <file>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $directories) {
                        if (count($directories) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->addTestsFromDirectories($directories);
                    },
                    ['-d', '--directories'],
                    '<directory>...',
                    $this->locale->_('Execute unit test files in all <directory>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $extensions) {
                        if (count($extensions) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->acceptTestFileExtensions($extensions);
                    },
                    ['-tfe', '--test-file-extensions'],
                    '<extension>...',
                    $this->locale->_('Execute unit test files with one of extensions <extension>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $patterns) {
                        if (count($patterns) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->addTestsFromPatterns($patterns);
                    },
                    ['-g', '--glob'],
                    '<pattern>...',
                    $this->locale->_('Execute unit test files which match <pattern>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $tags) {
                        if (count($tags) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->testTags($tags);
                    },
                    ['-t', '--tags'],
                    '<tag>...',
                    $this->locale->_('Execute only unit test with tags <tag>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $methods) {
                        if (count($methods) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->resetTestMethods();

                        foreach ($methods as $method) {
                            $method = explode('::', $method);

                            if (count($method) != 2) {
                                throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                            }

                            $script->testMethod($method[0], $method[1]);
                        }
                    },
                    ['-m', '--methods'],
                    '<class::method>...',
                    $this->locale->_('Execute all <class::method>, * may be used as wildcard for class name or method name')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $namespaces) {
                        if (count($namespaces) <= 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script
                            ->resetTestedNamespaces()
                            ->testNamespaces($namespaces)
                        ;
                    },
                    ['-ns', '--namespaces'],
                    '<namespace>...',
                    $this->locale->_('Execute all classes in all namespaces <namespace>')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if ($values) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->enableLoopMode();
                    },
                    ['-l', '--loop'],
                    null,
                    $this->locale->_('Execute tests in an infinite loop')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) !== 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->disableLoopMode();
                    },
                    ['--disable-loop-mode'],
                    null,
                    null,
                    3
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) !== 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->testIt();
                    },
                    ['--test-it'],
                    null,
                    $this->locale->_('Execute atoum unit tests')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if ($values) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        \mageekguy\atoum\cli::forceTerminal();
                    },
                    ['-ft', '--force-terminal'],
                    null,
                    $this->locale->_('Force output as in terminal')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->setAutoloaderFile($values[0]);
                    },
                    ['-af', '--autoloader-file'],
                    '<file>',
                    $this->locale->_('Include autoloader <file> before executing each test method'),
                    2
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->setBootstrapFile($values[0]);
                    },
                    ['-bf', '--bootstrap-file'],
                    '<file>',
                    $this->locale->_('Include bootstrap <file> before executing each test method'),
                    2
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) != 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $lightReport = new atoum\reports\realtime\cli\light();
                        $lightReport->addWriter($script->getOutputWriter());

                        $script->setReport($lightReport);
                    },
                    ['-ulr', '--use-light-report'],
                    null,
                    $this->locale->_('Use "light" CLI report'),
                    PHP_INT_MAX
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) != 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $lightReport = new atoum\reports\realtime\cli\dot();
                        $lightReport->addWriter($script->getOutputWriter());

                        $script->setReport($lightReport);
                    },
                    ['-udr', '--use-dot-report'],
                    null,
                    $this->locale->_('Use "dot" CLI report'),
                    PHP_INT_MAX
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) != 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $tapReport = new atoum\reports\realtime\tap();
                        $tapReport->addWriter($script->getOutputWriter());

                        $script->setReport($tapReport);
                    },
                    ['-utr', '--use-tap-report'],
                    null,
                    $this->locale->_('Use TAP report'),
                    PHP_INT_MAX
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) != 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->enableDebugMode();
                    },
                    ['--debug'],
                    null,
                    $this->locale->_('Enable debug mode')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more information'), $argument, $script->getName()));
                        }

                        $script->setXdebugConfig($values[0]);
                    },
                    ['-xc', '--xdebug-config'],
                    null,
                    $this->locale->_('Set XDEBUG_CONFIG variable')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        $script->failIfVoidMethods();
                    },
                    ['-fivm', '--fail-if-void-methods'],
                    null,
                    $this->locale->_('Make the test suite fail if there is at least one void test method')
                )
            ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        $script->failIfSkippedMethods();
                    },
                    ['-fism', '--fail-if-skipped-methods'],
                    null,
                    $this->locale->_('Make the test suite fail if there is at least one skipped test method')
                )
        ;

        $this->setDefaultArgumentHandler(
            function ($script, $argument) {
                try {
                    $script->getRunner()->addTest($argument);
                } catch (\exception $exception) {
                    return false;
                }

                return true;
            }
        );

        return $this;
    }

    protected function doRun()
    {
        parent::doRun();

        if ($this->argumentsParser->hasFoundArguments() === false) {
            $this->argumentsParser->parse($this, $this->defaultArguments);
        }

        if (count($this->runner->getTestPaths()) <= 0 && count($this->runner->getDeclaredTestClasses()) <= 0) {
            $this->writeError($this->locale->_('No test found'))->help();
        } else {
            $arguments = $this->argumentsParser->getValues();

            if (count($arguments) <= 0) {
                $this->verbose($this->locale->_('Using no CLI argument…'));
            } else {
                $this->verbose(sprintf($this->locale->__('Using %s CLI argument…', 'Using %s arguments…', count($arguments)), $this->argumentsParser));
            }

            if (count($this->configFiles) > 0) {
                foreach ($this->configFiles as $configFile) {
                    $this->verbose($this->locale->_('Using \'%s\' configuration file…', $configFile));
                }
            }

            $autoloaderFile = $this->runner->getAutoloaderFile();

            if ($autoloaderFile !== null) {
                $this->verbose($this->locale->_('Using \'%s\' autoloader file…', $autoloaderFile));
            }

            $bootstrapFile = $this->runner->getBootstrapFile();

            if ($bootstrapFile !== null) {
                $this->verbose($this->locale->_('Using \'%s\' bootstrap file…', $bootstrapFile));
            }

            foreach (atoum\autoloader::getRegisteredAutoloaders() as $autoloader) {
                $this->verbose($this->locale->_('Using \'%s\' autoloader cache file…', $autoloader->getCacheFileForInstance()));
            }

            foreach ($this->runner->getTestPaths() as $testPath) {
                $this->verbose($this->locale->_('Using \'%s\' test file…', $testPath), 2);
            }

            if ($this->loop === true) {
                $this->loop();
            } else {
                if ($this->runner->hasReports() === false) {
                    $this->addDefaultReport();
                }

                $methods = $this->methods;
                $oldFailMethods = [];

                if ($this->scoreFile !== null && ($scoreFileContents = @file_get_contents($this->scoreFile)) !== false && ($oldScore = @unserialize($scoreFileContents)) instanceof atoum\score) {
                    $oldFailMethods = self::getFailMethods($oldScore);

                    if ($oldFailMethods) {
                        $methods = $oldFailMethods;
                    }
                }

                $newScore = $this->runner->run($this->namespaces, $this->tags, $this->getClassesOf($methods), $methods);

                $this->saveScore($newScore);

                if ($oldFailMethods && count(self::getFailMethods($newScore)) <= 0) {
                    $testMethods = $this->runner->getTestMethods($this->namespaces, $this->tags, $this->methods);

                    if (count($testMethods) > 1 || count(current($testMethods)) > 1) {
                        $this->saveScore($this->runner->run($this->namespaces, $this->tags, $this->getClassesOf($this->methods), $this->methods));
                    }
                }
            }
        }

        return $this;
    }

    protected function loop()
    {
        $php = new php();
        $php
            ->addOption('-f', $_SERVER['argv'][0])
            ->addArgument('--disable-loop-mode')
        ;

        if ($this->cli->isTerminal() === true) {
            $php->addArgument('--force-terminal');
        }

        $addScoreFile = false;

        foreach ($this->argumentsParser->getValues() as $argument => $values) {
            switch ($argument) {
                case '-l':
                case '--loop':
                case '--disable-loop-mode':
                    break;

                case '-sf':
                case '--score-file':
                    $addScoreFile = true;
                    break;

                default:
                    if ($this->argumentsParser->argumentHasHandler($argument) === false) {
                        $min = null;
                        $closestArgument = $this->argumentsParser->getClosestArgument($argument, $min);

                        if ($closestArgument !== null && $min === 0) {
                            $php->addArgument($closestArgument, implode(' ', $values));
                        } else {
                            $php->addArgument('-f', $argument);
                        }
                    } else {
                        $php->addArgument($argument, implode(' ', $values));
                    }
            }
        }

        if ($this->scoreFile === null) {
            $this->scoreFile = sys_get_temp_dir() . '/atoum.score';

            @unlink($this->scoreFile);

            $addScoreFile = true;
        }

        if ($addScoreFile === true) {
            $php->addArgument('--score-file', $this->scoreFile);
        }

        while ($this->canRun() === true) {
            passthru((string) $php);

            if ($this->loop === false || $this->looper->runAgain() === false) {
                $this->stopRun();
            }
        }

        return $this;
    }

    protected function saveScore(atoum\score $score)
    {
        if ($this->scoreFile !== null && $this->adapter->file_put_contents($this->scoreFile, serialize($score), \LOCK_EX) === false) {
            throw new exceptions\runtime('Unable to save score in \'' . $this->scoreFile . '\'');
        }

        return $this;
    }

    protected function writeHelpUsage()
    {
        $this->writeHelp($this->locale->_('Usage: %s [path/to/test/file] [options]', $this->getName()) . PHP_EOL);

        return $this;
    }

    protected function parseArguments(array $arguments)
    {
        $configTestPaths = $this->runner->getTestPaths();

        $this->runner->resetTestPaths();

        parent::parseArguments($arguments);

        $this->runner->setTestPaths($this->runner->getTestPaths() ?: $configTestPaths);

        return $this;
    }

    private function getClassesOf($methods)
    {
        return count($methods) <= 0 || isset($methods['*']) === true ? [] : array_keys($methods);
    }

    private function copy($from, $to)
    {
        if (@$this->adapter->copy($from, $to) === false) {
            throw new exceptions\runtime($this->locale->_('Unable to write \'' . $from . '\' to \'' . $to . '\''));
        }

        return $this;
    }

    private static function getFailMethods(atoum\score $score)
    {
        return self::mergeMethods(self::mergeMethods(self::mergeMethods($score->getMethodsWithFail(), $score->getMethodsWithError()), $score->getMethodsWithException()), $score->getMethodsNotCompleted());
    }

    private static function mergeMethods(array $methods, array $newMethods)
    {
        foreach ($newMethods as $class => $classMethods) {
            if (isset($methods[$class]) === false) {
                $methods[$class] = $classMethods;
            } else {
                $methods[$class] = array_unique(array_merge($methods[$class], $classMethods));
            }
        }

        return $methods;
    }
}
