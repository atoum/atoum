<?php

namespace atoum\atoum;

use atoum\atoum\extension\aggregator;

class runner implements observable
{
    public const atoumVersionConstant = 'atoum\atoum\version';
    public const atoumDirectoryConstant = 'atoum\atoum\directory';

    public const runStart = 'runnerStart';
    public const runStop = 'runnerStop';

    protected ?runner\score $score = null;
    protected ?adapter $adapter = null;
    protected ?locale $locale = null;
    protected ?includer $includer = null;
    protected ?test\generator $testGenerator = null;
    protected ?\Closure $globIteratorFactory = null;
    protected ?\Closure $reflectionClassFactory = null;
    protected ?\Closure $testFactory = null;
    protected ?\splObjectStorage $observers = null;
    protected ?\splObjectStorage $reports = null;
    protected ?report $reportSet = null;
    protected array $testPaths = [];
    protected int $testNumber = 0;
    protected int $testMethodNumber = 0;
    protected bool $codeCoverage = true;
    protected bool $branchesAndPathsCoverage = false;
    protected ?php $php = null;
    protected ?string $defaultReportTitle = null;
    protected ?int $maxChildrenNumber = null;
    protected ?string $bootstrapFile = null;
    protected ?string $autoloaderFile = null;
    protected ?iterators\recursives\directory\factory $testDirectoryIterator = null;
    protected bool $debugMode = false;
    protected ?string $xdebugConfig = null;
    protected bool $failIfVoidMethods = false;
    protected bool $failIfSkippedMethods = false;
    protected bool $disallowUsageOfUndefinedMethodInMock = false;
    protected ?aggregator $extensions = null;

    private ?float $start = null;
    private ?float $stop = null;
    private bool $canAddTest = true;

    public function __construct()
    {
        $this
            ->setAdapter()
            ->setLocale()
            ->setIncluder()
            ->setScore()
            ->setPhp()
            ->setTestDirectoryIterator()
            ->setGlobIteratorFactory()
            ->setReflectionClassFactory()
            ->setTestFactory()
        ;

        $this->observers = new \splObjectStorage();
        $this->reports = new \splObjectStorage();
        $this->extensions = new aggregator();
    }

    public function setAdapter(?adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new adapter();

        return $this;
    }

    public function getAdapter(): adapter
    {
        return $this->adapter;
    }

    public function setLocale(?locale $locale = null): static
    {
        $this->locale = $locale ?: new locale();

        return $this;
    }

    public function getLocale(): locale
    {
        return $this->locale;
    }

    public function setIncluder(?includer $includer = null): static
    {
        $this->includer = $includer ?: new includer();

        return $this;
    }

    public function getIncluder(): includer
    {
        return $this->includer;
    }

    public function setScore(?runner\score $score = null): static
    {
        $this->score = $score ?: new runner\score();

        return $this;
    }

    public function getScore(): score
    {
        return $this->score;
    }

    public function setTestGenerator(?test\generator $generator = null): static
    {
        $this->testGenerator = $generator ?: new test\generator();

        return $this;
    }

    public function getTestGenerator(): ?test\generator
    {
        return $this->testGenerator;
    }

    public function setTestDirectoryIterator(?iterators\recursives\directory\factory $iterator = null): static
    {
        $this->testDirectoryIterator = $iterator ?: new iterators\recursives\directory\factory();

        return $this;
    }

    public function getTestDirectoryIterator(): iterators\recursives\directory\factory
    {
        return $this->testDirectoryIterator;
    }

    public function setGlobIteratorFactory(?\Closure $factory = null): static
    {
        $this->globIteratorFactory = $factory ?: function ($pattern) {
            return new \globIterator($pattern);
        };

        return $this;
    }

    public function getGlobIteratorFactory(): \Closure
    {
        return $this->globIteratorFactory;
    }

    public function setReflectionClassFactory(?\Closure $factory = null): static
    {
        $this->reflectionClassFactory = $factory ?: function ($class) {
            return new \reflectionClass($class);
        };

        return $this;
    }

    public function getReflectionClassFactory(): \Closure
    {
        return $this->reflectionClassFactory;
    }

    public function enableDebugMode(): static
    {
        $this->debugMode = true;

        return $this;
    }

    public function disableDebugMode(): static
    {
        $this->debugMode = false;

        return $this;
    }

    public function debugModeIsEnabled(): bool
    {
        return $this->debugMode;
    }

    public function disallowUndefinedMethodInInterface(): static
    {
        return $this->disallowUsageOfUndefinedMethodInMock();
    }

    public function disallowUsageOfUndefinedMethodInMock(): static
    {
        $this->disallowUsageOfUndefinedMethodInMock = true;

        return $this;
    }

    public function allowUndefinedMethodInInterface(): static
    {
        return $this->allowUsageOfUndefinedMethodInMock();
    }

    public function allowUsageOfUndefinedMethodInMock(): static
    {
        $this->disallowUsageOfUndefinedMethodInMock = false;

        return $this;
    }

    public function undefinedMethodInInterfaceAreAllowed(): bool
    {
        return $this->usageOfUndefinedMethodInMockAreAllowed();
    }

    public function usageOfUndefinedMethodInMockAreAllowed(): bool
    {
        return $this->disallowUsageOfUndefinedMethodInMock === false;
    }

    public function setXdebugConfig(?string $value): static
    {
        $this->xdebugConfig = $value;

        return $this;
    }

    public function getXdebugConfig(): ?string
    {
        return $this->xdebugConfig;
    }

    public function setMaxChildrenNumber(int $number): static
    {
        if ($number < 1) {
            throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
        }

        $this->maxChildrenNumber = $number;

        return $this;
    }

    public function acceptTestFileExtensions(array $testFileExtensions): static
    {
        $this->testDirectoryIterator->acceptExtensions($testFileExtensions);

        return $this;
    }

    public function setDefaultReportTitle(string $title): static
    {
        $this->defaultReportTitle = (string) $title;

        return $this;
    }

    public function setBootstrapFile(string $path): static
    {
        try {
            $this->includer->includePath($path, function ($path) {
                include_once($path);
            });
        } catch (includer\exception $exception) {
            throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to use bootstrap file \'%s\''), $path));
        }

        $this->bootstrapFile = $path;

        return $this;
    }

    public function setAutoloaderFile(string $path): static
    {
        try {
            $this->includer->includePath($path, function ($path) {
                include_once($path);
            });
        } catch (includer\exception $exception) {
            throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to use autoloader file \'%s\''), $path));
        }

        $this->autoloaderFile = $path;

        return $this;
    }

    public function getDefaultReportTitle(): ?string
    {
        return $this->defaultReportTitle;
    }

    public function setPhp(?php $php = null): static
    {
        $this->php = $php ?: new php();

        return $this;
    }

    public function getPhp(): php
    {
        return $this->php;
    }

    public function setPhpPath(string $path): static
    {
        $this->php->setBinaryPath($path);

        return $this;
    }

    public function getPhpPath(): string
    {
        return $this->php->getBinaryPath();
    }

    public function getTestNumber(): int
    {
        return $this->testNumber;
    }

    public function getTestMethodNumber(): int
    {
        return $this->testMethodNumber;
    }

    public function getObservers(): array
    {
        $observers = [];

        foreach ($this->observers as $observer) {
            $observers[] = $observer;
        }

        return $observers;
    }

    public function getBootstrapFile(): ?string
    {
        return $this->bootstrapFile;
    }

    public function getAutoloaderFile(): ?string
    {
        return $this->autoloaderFile;
    }

    public function getTestMethods(array $namespaces = [], array $tags = [], array $testMethods = [], ?string $testBaseClass = null): array
    {
        $classes = [];

        foreach ($this->getDeclaredTestClasses($testBaseClass) as $testClass) {
            $test = new $testClass();

            if ($test->isIgnored($namespaces, $tags) === false) {
                $methods = $test->runTestMethods($testMethods, $tags);

                if ($methods) {
                    $classes[$testClass] = $methods;
                }
            }
        }

        return $classes;
    }

    public function getCoverage(): score\coverage
    {
        return $this->score->getCoverage();
    }

    public function enableCodeCoverage(): static
    {
        $this->codeCoverage = true;

        return $this;
    }

    public function disableCodeCoverage(): static
    {
        $this->codeCoverage = false;

        return $this;
    }

    public function codeCoverageIsEnabled(): bool
    {
        return $this->codeCoverage;
    }

    public function enableBranchesAndPathsCoverage(): static
    {
        $this->branchesAndPathsCoverage = $this->codeCoverageIsEnabled();

        return $this;
    }

    public function disableBranchesAndPathsCoverage(): static
    {
        $this->branchesAndPathsCoverage = false;

        return $this;
    }

    public function branchesAndPathsCoverageIsEnabled(): bool
    {
        return $this->branchesAndPathsCoverage;
    }

    public function doNotfailIfVoidMethods(): static
    {
        $this->failIfVoidMethods = false;

        return $this;
    }

    public function failIfVoidMethods(): static
    {
        $this->failIfVoidMethods = true;

        return $this;
    }

    public function shouldFailIfVoidMethods(): bool
    {
        return $this->failIfVoidMethods;
    }

    public function doNotfailIfSkippedMethods(): static
    {
        $this->failIfSkippedMethods = false;

        return $this;
    }

    public function failIfSkippedMethods(): static
    {
        $this->failIfSkippedMethods = true;

        return $this;
    }

    public function shouldFailIfSkippedMethods(): bool
    {
        return $this->failIfSkippedMethods;
    }

    public function addObserver(observer $observer): static
    {
        $this->observers->offsetSet($observer);

        return $this;
    }

    public function removeObserver(observer $observer): static
    {
        $this->observers->offsetUnset($observer);

        return $this;
    }

    public function callObservers(string $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->handleEvent($event, $this);
        }
    }

    public function setPathAndVersionInScore(): static
    {
        $this->score
            ->setAtoumVersion($this->adapter->defined(static::atoumVersionConstant) === false ? null : $this->adapter->constant(static::atoumVersionConstant))
            ->setAtoumPath($this->adapter->defined(static::atoumDirectoryConstant) === false ? null : $this->adapter->constant(static::atoumDirectoryConstant))
        ;

        if ($this->php->reset()->addOption('--version')->run()->getExitCode() > 0) {
            throw new exceptions\runtime("Unable to get PHP version from '" . $this->php . "'");
        }

        $this->score
            ->setPhpPath($this->php->getBinaryPath())
            ->setPhpVersion($this->php->getStdout())
        ;

        return $this;
    }

    public function getTestFactory(): \Closure
    {
        return $this->testFactory;
    }

    public function setTestFactory(?\Closure $testFactory = null): static
    {
        $testFactory = $testFactory ?: function ($testClass) {
            return new $testClass();
        };

        $this->testFactory = function ($testClass) use ($testFactory) {
            $test = call_user_func($testFactory, $testClass);

            if ($this->usageOfUndefinedMethodInMockAreAllowed() === false) {
                $test->getMockGenerator()->disallowUndefinedMethodUsage();
            }

            return $test;
        };

        return $this;
    }

    public function run(array $namespaces = [], array $tags = [], array $runTestClasses = [], array $runTestMethods = [], ?string $testBaseClass = null): runner\score
    {
        $this->includeTestPaths();

        $this->testNumber = 0;
        $this->testMethodNumber = 0;

        $this->score->reset();

        $this->setPathAndVersionInScore();

        if ($this->defaultReportTitle !== null) {
            foreach ($this->reports as $report) {
                if ($report->getTitle() === null) {
                    $report->setTitle($this->defaultReportTitle);
                }
            }
        }

        $declaredTestClasses = $this->getDeclaredTestClasses($testBaseClass);

        if (count($runTestClasses) <= 0) {
            $runTestClasses = $declaredTestClasses;
        } else {
            $runTestClasses = array_intersect($runTestClasses, $declaredTestClasses);
        }

        natsort($runTestClasses);

        $tests = [];

        foreach ($runTestClasses as $runTestClass) {
            $test = call_user_func($this->testFactory, $runTestClass);

            if ($test->isIgnored($namespaces, $tags) === false) {
                $testMethodNumber = count($test->runTestMethods($runTestMethods, $tags));

                if ($testMethodNumber > 0) {
                    $tests[] = $test;
                    $test->addExtensions($this->extensions);

                    $this->testNumber++;
                    $this->testMethodNumber += $testMethodNumber;

                    $test
                        ->setPhpPath($this->php->getBinaryPath())
                        ->setAdapter($this->adapter)
                        ->setLocale($this->locale)
                        ->setBootstrapFile($this->bootstrapFile)
                        ->setAutoloaderFile($this->autoloaderFile)
                    ;

                    if ($this->debugMode === true) {
                        $test->enableDebugMode();
                    }

                    $test->setXdebugConfig($this->xdebugConfig);

                    if ($this->maxChildrenNumber !== null) {
                        $test->setMaxChildrenNumber($this->maxChildrenNumber);
                    }

                    if ($this->codeCoverageIsEnabled() === false) {
                        $test->disableCodeCoverage();
                    } else {
                        if ($this->branchesAndPathsCoverageIsEnabled()) {
                            $test->enableBranchesAndPathsCoverage();
                        }

                        $test->getScore()->setCoverage($this->getCoverage());
                    }

                    foreach ($this->observers as $observer) {
                        $test->addObserver($observer);
                    }
                }
            }
        }

        $this->start = $this->adapter->microtime(true);

        $this->callObservers(self::runStart);

        foreach ($tests as $test) {
            $this->score->merge($test->run()->getScore());
        }

        $this->stop = $this->adapter->microtime(true);

        $this->callObservers(self::runStop);

        return $this->score;
    }

    public function getTestPaths(): array
    {
        return $this->testPaths;
    }

    public function setTestPaths(array $testPaths): static
    {
        $this->testPaths = $testPaths;

        return $this;
    }

    public function resetTestPaths(): static
    {
        $this->testPaths = [];

        return $this;
    }

    public function canAddTest(): static
    {
        $this->canAddTest = true;

        return $this;
    }

    public function canNotAddTest(): static
    {
        $this->canAddTest = false;

        return $this;
    }

    public function addTest(string $path): static
    {
        if ($this->canAddTest === true) {
            $path = (string) $path;

            if (in_array($path, $this->testPaths) === false) {
                $this->testPaths[] = $path;
            }
        }

        return $this;
    }

    public function addTestsFromDirectory(string $directory): static
    {
        try {
            $paths = [];

            foreach (new \recursiveIteratorIterator($this->testDirectoryIterator->getIterator($directory)) as $path) {
                $paths[] = $path;
            }
        } catch (\UnexpectedValueException $exception) {
            throw new exceptions\runtime('Unable to read test directory \'' . $directory . '\'');
        }

        natcasesort($paths);

        foreach ($paths as $path) {
            $this->addTest($path);
        }

        return $this;
    }

    public function addTestsFromPattern(string $pattern): static
    {
        try {
            $paths = [];

            foreach (call_user_func($this->globIteratorFactory, rtrim($pattern, DIRECTORY_SEPARATOR)) as $path) {
                $paths[] = $path;
            }
        } catch (\UnexpectedValueException $exception) {
            throw new exceptions\runtime('Unable to read test from pattern \'' . $pattern . '\'');
        }

        natcasesort($paths);

        foreach ($paths as $path) {
            if ($path->isDir() === false) {
                $this->addTest($path);
            } else {
                $this->addTestsFromDirectory($path);
            }
        }

        return $this;
    }

    public function getRunningDuration(): ?float
    {
        return ($this->start === null || $this->stop === null ? null : $this->stop - $this->start);
    }

    public function getDeclaredTestClasses(?string $testBaseClass = null): array
    {
        return $this->findTestClasses($testBaseClass);
    }

    public function setReport(report $report): static
    {
        if ($this->reportSet === null) {
            $this->removeReports($report)->addReport($report);

            $this->reportSet = $report;
        }

        return $this;
    }

    public function addReport(report $report): static
    {
        if ($this->reportSet === null || $this->reportSet->isOverridableBy($report)) {
            $this->reports->offsetSet($report);

            $this->addObserver($report);
        }

        return $this;
    }

    public function removeReport(report $report): static
    {
        if ($this->reportSet === $report) {
            $this->reportSet = null;
        }

        $this->reports->offsetUnset($report);

        return $this->removeObserver($report);
    }

    public function removeReports(?report $override = null): static
    {
        if ($override === null) {
            foreach ($this->reports as $report) {
                $this->removeObserver($report);
            }

            $this->reports = new \splObjectStorage();
        } else {
            foreach ($this->reports as $report) {
                if ($report->isOverridableBy($override) === true) {
                    continue;
                }

                $this->removeObserver($report);
                $this->reports->offsetUnset($report);
            }
        }

        $this->reportSet = null;

        return $this;
    }

    public function hasReports(): bool
    {
        return (count($this->reports) > 0);
    }

    public function getReports(): array
    {
        $reports = [];

        foreach ($this->reports as $report) {
            $reports[] = $report;
        }

        return $reports;
    }

    public function getExtension(string $className): extension
    {
        foreach ($this->getExtensions() as $extension) {
            if (get_class($extension) === $className) {
                return $extension;
            }
        }

        throw new exceptions\logic\invalidArgument(sprintf('Extension %s is not loaded', $className));
    }

    public function getExtensions(): aggregator
    {
        return $this->extensions;
    }

    public function removeExtension(string|object $extension): static
    {
        if (is_object($extension) === true) {
            $extension = get_class($extension);
        }

        $extension = $this->getExtension($extension);
        $this->extensions->offsetUnset($extension);

        return $this->removeObserver($extension);
    }

    public function removeExtensions(): static
    {
        foreach ($this->extensions as $extension) {
            $this->removeObserver($extension);
        }

        $this->extensions = new aggregator();

        return $this;
    }

    public function addExtension(extension $extension, ?extension\configuration $configuration = null): static
    {
        if ($this->extensions->offsetExists($extension) === false) {
            $extension->setRunner($this);
            $this->addObserver($extension);
        }

        $this->extensions->offsetSet($extension, $configuration);

        return $this;
    }

    public static function isIgnored(test $test, array $namespaces, array $tags): bool
    {
        $isIgnored = $test->isIgnored();

        if ($isIgnored === false && $namespaces) {
            $classNamespace = strtolower($test->getClassNamespace());

            $isIgnored = count(array_filter($namespaces, function ($value) use ($classNamespace) {
                return strpos($classNamespace, strtolower($value)) === 0;
            })) <= 0;
        }

        if ($isIgnored === false && $tags) {
            $isIgnored = count($testTags = $test->getAllTags()) <= 0 || count(array_intersect($tags, $testTags)) == 0;
        }

        return $isIgnored;
    }

    protected function findTestClasses(?string $testBaseClass = null): array
    {
        $reflectionClassFactory = $this->reflectionClassFactory;
        $testBaseClass = $testBaseClass ?: __NAMESPACE__ . '\test';

        return array_filter(
            $this->adapter->get_declared_classes(),
            function ($class) use ($reflectionClassFactory, $testBaseClass) {
                $class = $reflectionClassFactory($class);

                return ($class->isSubClassOf($testBaseClass) === true && $class->isAbstract() === false);
            }
        );
    }

    private function includeTestPaths(): static
    {
        $runner = $this;
        $includer = function ($path) use ($runner) {
            include_once($path);
        };

        foreach ($this->testPaths as $testPath) {
            try {
                $declaredTestClasses = $this->findTestClasses();
                $numberOfIncludedFiles = count(get_included_files());

                $this->includer->includePath($testPath, $includer);

                if ($numberOfIncludedFiles < count(get_included_files()) && count(array_diff($this->findTestClasses(), $declaredTestClasses)) <= 0 && $this->testGenerator !== null) {
                    $this->testGenerator->generate($testPath);

                    try {
                        $this->includer->includePath($testPath, function ($testPath) use ($runner) {
                            include($testPath);
                        });
                    } catch (includer\exception $exception) {
                        throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to add test file \'%s\''), $testPath));
                    }
                }
            } catch (includer\exception $exception) {
                if ($this->testGenerator === null) {
                    throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to add test file \'%s\''), $testPath));
                } else {
                    $this->testGenerator->generate($testPath);

                    try {
                        $this->includer->includePath($testPath, $includer);
                    } catch (includer\exception $exception) {
                        throw new exceptions\runtime\file(sprintf($this->getLocale()->_('Unable to generate test file \'%s\''), $testPath));
                    }
                }
            }
        }

        return $this;
    }
}
