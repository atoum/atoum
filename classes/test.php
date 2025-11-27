<?php

namespace atoum\atoum;

use atoum\atoum\tools\variable\analyzer;

abstract class test implements observable, \countable
{
    public const testMethodPrefix = 'test';
    public const defaultNamespace = '#(?:^|\\\)tests?\\\units?\\\#i';
    public const defaultMethodPrefix = '#^(?:test|_*[^_]+_should_)#i';
    public const runStart = 'testRunStart';
    public const beforeSetUp = 'beforeTestSetUp';
    public const afterSetUp = 'afterTestSetUp';
    public const beforeTestMethod = 'beforeTestMethod';
    public const fail = 'testAssertionFail';
    public const error = 'testError';
    public const void = 'testVoid';
    public const uncompleted = 'testUncompleted';
    public const skipped = 'testSkipped';
    public const exception = 'testException';
    public const runtimeException = 'testRuntimeException';
    public const success = 'testAssertionSuccess';
    public const afterTestMethod = 'afterTestMethod';
    public const beforeTearDown = 'beforeTestTearDown';
    public const afterTearDown = 'afterTestTearDown';
    public const runStop = 'testRunStop';
    public const defaultEngine = 'concurrent';
    public const enginesNamespace = '\atoum\atoum\test\engines';

    private ?test\score $score = null;
    private ?locale $locale = null;
    private ?adapter $adapter = null;
    private ?mock\generator $mockGenerator = null;
    private ?autoloader\mock $mockAutoloader = null;
    private ?factory\builder $factoryBuilder = null;
    private ?\Closure $reflectionMethodFactory = null;
    private ?\Closure $phpExtensionFactory = null;
    private ?asserter\generator $asserterGenerator = null;
    private ?test\assertion\manager $assertionManager = null;
    private ?php\mocker\funktion $phpFunctionMocker = null;
    private ?php\mocker\constant $phpConstantMocker = null;
    private ?test\adapter\storage $testAdapterStorage = null;
    private ?asserters\adapter\call\manager $asserterCallManager = null;
    private ?mock\controller\linker $mockControllerLinker = null;
    private ?string $phpPath = null;
    private ?string $testedClassName = null;
    private ?string $testedClassPath = null;
    private ?string $currentMethod = null;
    private ?string $testNamespace = null;
    private ?string $testMethodPrefix = null;
    private ?string $classEngine = null;
    private ?string $bootstrapFile = null;
    private ?string $autoloaderFile = null;
    private ?int $maxAsynchronousEngines = null;
    private int $asynchronousEngines = 0;
    private readonly string $path;
    private readonly string $class;
    private readonly string $classNamespace;
    private ?\splObjectStorage $observers = null;
    private array $tags = [];
    private array $phpVersions = [];
    private array $mandatoryExtensions = [];
    private array $supportedOs = [];
    private array $dataProviders = [];
    private array $testMethods = [];
    private array $runTestMethods = [];
    private array $engines = [];
    private array $methodEngines = [];
    private array $methodsAreNotVoid = [];
    private array $executeOnFailure = [];
    private bool $ignore = false;
    private bool $debugMode = false;
    private ?string $xdebugConfig = null;
    private bool $codeCoverage = false;
    private bool $branchesAndPathsCoverage = false;
    private bool $classHasNotVoidMethods = false;
    private ?\splObjectStorage $extensions = null;
    private ?analyzer $analyzer = null;

    private static ?string $namespace = null;
    private static ?string $methodPrefix = null;
    private static $defaultEngine = self::defaultEngine;

    public function __construct(?adapter $adapter = null, ?annotations\extractor $annotationExtractor = null, ?asserter\generator $asserterGenerator = null, ?test\assertion\manager $assertionManager = null, ?\Closure $reflectionClassFactory = null, ?\Closure $phpExtensionFactory = null, ?analyzer $analyzer = null)
    {
        $this
            ->setAdapter($adapter)
            ->setPhpFunctionMocker()
            ->setPhpConstantMocker()
            ->setMockGenerator()
            ->setMockAutoloader()
            ->setAsserterGenerator($asserterGenerator)
            ->setAssertionManager($assertionManager)
            ->setTestAdapterStorage()
            ->setMockControllerLinker()
            ->setScore()
            ->setLocale()
            ->setFactoryBuilder()
            ->setReflectionMethodFactory()
            ->setAsserterCallManager()
            ->enableCodeCoverage()
            ->setPhpExtensionFactory($phpExtensionFactory)
            ->setAnalyzer($analyzer);

        $this->observers = new \splObjectStorage();
        $this->extensions = new \splObjectStorage();

        $class = ($reflectionClassFactory ? $reflectionClassFactory($this) : new \reflectionClass($this));

        $this->path = $class->getFilename();
        $this->class = $class->getName();
        $this->classNamespace = $class->getNamespaceName();

        if ($annotationExtractor === null) {
            $annotationExtractor = new annotations\extractor();
        }

        $this->setClassAnnotations($annotationExtractor);

        $this->applyClassAttributes($class);

        $annotationExtractor->extract($class->getDocComment());

        if ($this->testNamespace === null || $this->testMethodPrefix === null) {
            $annotationExtractor
                ->unsetHandler('ignore')
                ->unsetHandler('tags')
                ->unsetHandler('maxChildrenNumber');

            $parentClass = $class;

            while (($this->testNamespace === null || $this->testMethodPrefix === null) && ($parentClass = $parentClass->getParentClass()) !== false) {
                $annotationExtractor->extract($parentClass->getDocComment());

                if ($this->testNamespace !== null) {
                    $annotationExtractor->unsetHandler('namespace');
                }

                if ($this->testMethodPrefix !== null) {
                    $annotationExtractor->unsetHandler('methodPrefix');
                }
            }
        }

        $this->setMethodAnnotations($annotationExtractor, $methodName);

        $testMethodPrefix = $this->getTestMethodPrefix();

        if ($this->analyzer->isRegex($testMethodPrefix) === false) {
            $testMethodFilter = function ($methodName) use ($testMethodPrefix) {
                return (stripos($methodName, $testMethodPrefix) === 0);
            };
        } else {
            $testMethodFilter = function ($methodName) use ($testMethodPrefix) {
                return (preg_match($testMethodPrefix, $methodName) == true);
            };
        }

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $publicMethod) {
            $methodName = $publicMethod->getName();

            if ($testMethodFilter($methodName) == true) {
                $this->testMethods[$methodName] = [];

                $this->applyMethodAttributes($publicMethod);

                $annotationExtractor->extract($publicMethod->getDocComment());

                if ($this->methodIsIgnored($methodName) === false && $publicMethod->getNumberOfParameters() > 0 && isset($this->dataProviders[$methodName]) === false) {
                    $this->setDataProvider($methodName);
                }
            }
        }

        $this->runTestMethods($this->getTestMethods());
    }

    public function __toString(): string
    {
        return $this->getClass();
    }

    public function __get(string $property): mixed
    {
        return $this->assertionManager->__get($property);
    }

    public function __set(string $property, mixed $handler): void
    {
        $this->assertionManager->{$property} = $handler;
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->assertionManager->__call($method, $arguments);
    }

    public function setAnalyzer(?analyzer $analyzer = null): static
    {
        $this->analyzer = $analyzer ?: new analyzer();

        return $this;
    }

    public function getAnalyzer(): analyzer
    {
        return $this->analyzer;
    }

    public function setTestAdapterStorage(?test\adapter\storage $storage = null): static
    {
        $this->testAdapterStorage = $storage ?: new test\adapter\storage();

        return $this;
    }

    public function getTestAdapterStorage(): test\adapter\storage
    {
        return $this->testAdapterStorage;
    }

    public function setMockControllerLinker(?mock\controller\linker $linker = null): static
    {
        $this->mockControllerLinker = $linker ?: new mock\controller\linker();

        return $this;
    }

    public function getMockControllerLinker(): mock\controller\linker
    {
        return $this->mockControllerLinker;
    }

    public function setScore(?test\score $score = null): static
    {
        $this->score = $score ?: new test\score();

        return $this;
    }

    public function getScore(): score
    {
        return $this->score;
    }

    public function setLocale(?locale $locale = null): static
    {
        $this->locale = $locale ?: new locale();

        return $this;
    }

    public function getLocale(): ?locale
    {
        return $this->locale;
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

    public function setPhpMocker(?php\mocker $phpMocker = null): static
    {
        $phpMocker = $phpMocker ?: new php\mocker();

        $phpMocker->addToTest($this);

        return $this;
    }

    public function setPhpFunctionMocker(?php\mocker\funktion $phpFunctionMocker = null): static
    {
        $this->phpFunctionMocker = $phpFunctionMocker ?: new php\mocker\funktion();

        return $this;
    }

    public function getPhpFunctionMocker(): php\mocker\funktion
    {
        return $this->phpFunctionMocker;
    }

    public function setPhpConstantMocker(?php\mocker\constant $phpConstantMocker = null): static
    {
        $this->phpConstantMocker = $phpConstantMocker ?: new php\mocker\constant();

        return $this;
    }

    public function getPhpConstantMocker(): php\mocker\constant
    {
        return $this->phpConstantMocker;
    }

    public function setMockGenerator(?test\mock\generator $generator = null): static
    {
        if ($generator !== null) {
            $generator->setTest($this);
        } else {
            $generator = new test\mock\generator($this);
        }

        $this->mockGenerator = $generator;

        return $this;
    }

    public function getMockGenerator(): test\mock\generator
    {
        return $this->mockGenerator;
    }

    public function setMockAutoloader(?autoloader\mock $autoloader = null): static
    {
        $this->mockAutoloader = $autoloader ?: new autoloader\mock();

        return $this;
    }

    public function getMockAutoloader(): autoloader\mock
    {
        return $this->mockAutoloader;
    }

    public function setFactoryBuilder(?factory\builder $factoryBuilder = null): static
    {
        $this->factoryBuilder = $factoryBuilder ?: new factory\builder\Closure();

        return $this;
    }

    public function getFactoryBuilder(): factory\builder
    {
        return $this->factoryBuilder;
    }

    public function setReflectionMethodFactory(?\Closure $factory = null): static
    {
        $this->reflectionMethodFactory = $factory ?: function ($class, $method) {
            return new \reflectionMethod($class, $method);
        };

        return $this;
    }

    public function setPhpExtensionFactory(?\Closure $factory = null): static
    {
        $this->phpExtensionFactory = $factory ?: function ($extensionName) {
            return new php\extension($extensionName);
        };

        return $this;
    }

    public function setAsserterGenerator(?test\asserter\generator $generator = null): static
    {
        if ($generator !== null) {
            $generator->setTest($this);
        } else {
            $generator = new test\asserter\generator($this);
        }

        $this->asserterGenerator = $generator->setTest($this);

        return $this;
    }

    public function getAsserterGenerator(): asserter\generator
    {
        $this->testAdapterStorage->resetCalls();

        return $this->asserterGenerator;
    }

    public function setAssertionManager(?test\assertion\manager $assertionManager = null): static
    {
        $this->assertionManager = $assertionManager ?: new test\assertion\manager();

        $this->assertionManager
            ->setHandler('when', function ($mixed) {
                if ($mixed instanceof \Closure) {
                    $mixed();
                }
                return $this;
            })
            ->setHandler('assert', function ($case = null) {
                $this->stopCase();
                if ($case !== null) {
                    $this->startCase($case);
                }
                return $this;
            })
            ->setHandler('mockGenerator', function () {
                return $this->getMockGenerator();
            })
            ->setHandler('mockClass', function ($class, $mockNamespace = null, $mockClass = null) {
                $this->getMockGenerator()->generate($class, $mockNamespace, $mockClass);
                return $this;
            })
            ->setHandler('mockTestedClass', function ($mockNamespace = null, $mockClass = null) {
                $this->getMockGenerator()->generate($this->getTestedClassName(), $mockNamespace, $mockClass);
                return $this;
            })
            ->setHandler(
                'newMockInstance',
                function ($class, $mockNamespace = null, $mockClass = null, ?array $constructorArguments = null) {
                    $mockNamespace = trim($mockNamespace ?: $this->getMockGenerator()->getDefaultNamespace(), '\\');
                    $mockClass = trim($mockClass ?: $class, '\\');
                    $className = $mockNamespace . '\\' . $mockClass;

                    if (class_exists($className) === false) {
                        $this->mockClass($class, $mockNamespace, $mockClass);
                    }

                    if ($constructorArguments !== null) {
                        $reflectionClass = new \reflectionClass($className);

                        return $reflectionClass->newInstanceArgs($constructorArguments);
                    }

                    return new $className();
                }
            )
            ->setHandler('dump', function (...$arguments) {
                if ($this->debugModeIsEnabled() === true) {
                    call_user_func_array('var_dump', $arguments);
                }
                return $this;
            })
            ->setHandler('stop', function () {
                if ($this->debugModeIsEnabled() === true) {
                    throw new test\exceptions\stop();
                }
                return $this;
            })
            ->setHandler('executeOnFailure', function ($callback) {
                if ($this->debugModeIsEnabled() === true) {
                    $this->executeOnFailure($callback);
                }
                return $this;
            })
            ->setHandler('dumpOnFailure', function ($variable) {
                if ($this->debugModeIsEnabled() === true) {
                    $this->executeOnFailure(function () use ($variable) {
                        var_dump($variable);
                    });
                }
                return $this;
            })
            ->setPropertyHandler('function', function () {
                return $this->getPhpFunctionMocker();
            })
            ->setPropertyHandler('constant', function () {
                return $this->getPhpConstantMocker();
            })
            ->setPropertyHandler('exception', function () {
                return asserters\exception::getLastValue();
            });

        $this->assertionManager
            ->setHandler('callStaticOnTestedClass', function ($method, ...$arguments) {
                return call_user_func_array([$this->getTestedClassName(), $method], $arguments);
            });

        $mockGenerator = $this->mockGenerator;

        $this->assertionManager
            ->setPropertyHandler('nextMockedMethod', function () use ($mockGenerator) {
                return $mockGenerator->getMethod();
            });

        $returnTest = function () {
            return $this;
        };

        $this->assertionManager
            ->setHandler('if', $returnTest)
            ->setHandler('and', $returnTest)
            ->setHandler('then', $returnTest)
            ->setHandler('given', $returnTest)
            ->setMethodHandler('define', $returnTest)
            ->setMethodHandler('let', $returnTest);

        $returnMockController = function (mock\aggregator $mock) {
            return $mock->getMockController();
        };

        $this->assertionManager
            ->setHandler('calling', $returnMockController)
            ->setHandler('ƒ', $returnMockController);

        $this->assertionManager
            ->setHandler('resetMock', function (mock\aggregator $mock) {
                return $mock->getMockController()->resetCalls();
            })
            ->setHandler('resetAdapter', function (test\adapter $adapter) {
                return $adapter->resetCalls();
            });

        $phpFunctionMocker = $this->phpFunctionMocker;

        $this->assertionManager->setHandler('resetFunction', function (test\adapter\invoker $invoker) use ($phpFunctionMocker) {
            $phpFunctionMocker->resetCalls($invoker->getFunction());
            return $invoker;
        });

        $assertionAliaser = $this->assertionManager->getAliaser();

        $this->assertionManager
            ->setPropertyHandler('define', function () use ($assertionAliaser) {
                return $assertionAliaser;
            })
            ->setHandler('from', function ($class) use ($assertionAliaser) {
                $assertionAliaser->from($class);
                return $this;
            })
            ->setHandler('use', function ($target) use ($assertionAliaser) {
                $assertionAliaser->alias($target);
                return $this;
            })
            ->setHandler('as', function ($alias) use ($assertionAliaser) {
                $assertionAliaser->to($alias);
                return $this;
            });

        $asserterGenerator = $this->asserterGenerator;

        $this->assertionManager->setDefaultHandler(
            function ($keyword, $arguments) use ($asserterGenerator, $assertionAliaser) {
                static $lastAsserter = null;

                if ($lastAsserter !== null) {
                    $realKeyword = $assertionAliaser->resolveAlias($keyword, get_class($lastAsserter));

                    if ($realKeyword !== $keyword) {
                        return call_user_func_array([$lastAsserter, $realKeyword], $arguments);
                    }
                }

                return ($lastAsserter = $asserterGenerator->getAsserterInstance($keyword, $arguments));
            }
        );

        $this->assertionManager
            ->use('phpObject')->as('object')
            ->use('phpArray')->as('array')
            ->use('phpArray')->as('in')
            ->use('phpClass')->as('class')
            ->use('phpFunction')->as('function')
            ->use('phpFloat')->as('float')
            ->use('phpString')->as('string')
            ->use('phpResource')->as('resource')
            ->use('calling')->as('method');

        return $this;
    }

    public function getAsserterCallManager(): asserters\adapter\call\manager
    {
        return $this->asserterCallManager;
    }

    public function setAsserterCallManager(?asserters\adapter\call\manager $asserterCallManager = null): static
    {
        $this->asserterCallManager = $asserterCallManager ?: new asserters\adapter\call\manager();

        return $this;
    }

    public function addClassPhpVersion(string $version, ?string $operator = null): static
    {
        $this->phpVersions[$version] = $operator ?: '>=';

        return $this;
    }

    public function getClassPhpVersions(): array
    {
        return $this->phpVersions;
    }

    public function addClassSupportedOs(string $os): static
    {
        $this->supportedOs[] = strtolower($os);

        return $this;
    }

    public function getClassSupportedOs(): array
    {
        return $this->supportedOs;
    }

    public function addMandatoryClassExtension(string $extension): static
    {
        $this->mandatoryExtensions[] = $extension;

        return $this;
    }

    public function addMethodSupportedOs(string $testMethodName, string $os): static
    {
        $this->checkMethod($testMethodName)->testMethods[$testMethodName]['os'][] = strtolower($os);

        return $this;
    }

    public function getMethodSupportedOs(?string $testMethodName = null): array
    {
        $supportedOs = [];

        $classSupportedOs = $this->getClassSupportedOs();
        $mergeSupportedOs = function ($classOs, $methodOs) {
            return array_merge(
                array_filter(
                    $classOs,
                    function ($os) use ($methodOs) {
                        return array_search(trim($os, '!'), $methodOs, true) === false
                            && array_search('!' . trim($os, '!'), $methodOs, true) === false;
                    }
                ),
                $methodOs
            );
        };

        if ($testMethodName === null) {
            foreach ($this->testMethods as $testMethodName => $annotations) {
                if (isset($annotations['os']) === false) {
                    $supportedOs[$testMethodName] = $classSupportedOs;
                } else {
                    $supportedOs[$testMethodName] = $mergeSupportedOs($classSupportedOs, $annotations['os']);
                }
            }
        } else {
            if (isset($this->checkMethod($testMethodName)->testMethods[$testMethodName]['os']) === false) {
                $supportedOs = $classSupportedOs;
            } else {
                $supportedOs = $mergeSupportedOs($classSupportedOs, $this->testMethods[$testMethodName]['os']);
            }
        }

        return $supportedOs;
    }

    public function addMethodPhpVersion(?string $testMethodName, string $version, ?string $operator = null): static
    {
        $this->checkMethod($testMethodName)->testMethods[$testMethodName]['php'][$version] = $operator ?: '>=';

        return $this;
    }

    public function getMethodPhpVersions(?string $testMethodName = null): array
    {
        $versions = [];

        $classVersions = $this->getClassPhpVersions();

        if ($testMethodName === null) {
            foreach ($this->testMethods as $testMethodName => $annotations) {
                if (isset($annotations['php']) === false) {
                    $versions[$testMethodName] = $classVersions;
                } else {
                    $versions[$testMethodName] = array_merge($classVersions, $annotations['php']);
                }
            }
        } else {
            if (isset($this->checkMethod($testMethodName)->testMethods[$testMethodName]['php']) === false) {
                $versions = $classVersions;
            } else {
                $versions = array_merge($classVersions, $this->testMethods[$testMethodName]['php']);
            }
        }

        return $versions;
    }

    public function getMandatoryClassExtensions(): array
    {
        return $this->mandatoryExtensions;
    }

    public function addMandatoryMethodExtension(string $testMethodName, string $extension): static
    {
        $this->checkMethod($testMethodName)->testMethods[$testMethodName]['mandatoryExtensions'][] = $extension;

        return $this;
    }

    public function getMandatoryMethodExtensions(?string $testMethodName = null): array
    {
        $extensions = [];

        $mandatoryClassExtensions = $this->getMandatoryClassExtensions();

        if ($testMethodName === null) {
            foreach ($this->testMethods as $testMethodName => $annotations) {
                if (isset($annotations['mandatoryExtensions']) === false) {
                    $extensions[$testMethodName] = $mandatoryClassExtensions;
                } else {
                    $extensions[$testMethodName] = array_merge($mandatoryClassExtensions, $annotations['mandatoryExtensions']);
                }
            }
        } else {
            if (isset($this->checkMethod($testMethodName)->testMethods[$testMethodName]['mandatoryExtensions']) === false) {
                $extensions = $mandatoryClassExtensions;
            } else {
                $extensions = array_merge($mandatoryClassExtensions, $this->testMethods[$testMethodName]['mandatoryExtensions']);
            }
        }

        return $extensions;
    }

    public function skip(string $message): never
    {
        throw new test\exceptions\skip($message);
    }

    public function getAssertionManager(): test\assertion\manager
    {
        return $this->assertionManager;
    }

    public function setClassEngine(string $engine): static
    {
        $this->classEngine = $engine;

        return $this;
    }

    public function getClassEngine(): ?string
    {
        return $this->classEngine;
    }

    public function classHasVoidMethods(): void
    {
        $this->classHasNotVoidMethods = false;
    }

    public function classHasNotVoidMethods(): void
    {
        $this->classHasNotVoidMethods = true;
    }

    public function setMethodVoid(string $method): void
    {
        $this->methodsAreNotVoid[$method] = false;
    }

    public function setMethodNotVoid(string $method): void
    {
        $this->methodsAreNotVoid[$method] = true;
    }

    public function methodIsNotVoid(string $method): bool
    {
        return (isset($this->methodsAreNotVoid[$method]) === false ? $this->classHasNotVoidMethods : $this->methodsAreNotVoid[$method]);
    }

    public function setMethodEngine(string $method, string $engine): static
    {
        $this->methodEngines[$method] = $engine;

        return $this;
    }

    public function getMethodEngine(string $method): ?string
    {
        return $this->methodEngines[$method] ?? null;
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

    public function setXdebugConfig(?string $value): static
    {
        $this->xdebugConfig = $value;

        return $this;
    }

    public function getXdebugConfig(): ?string
    {
        return $this->xdebugConfig;
    }

    public function executeOnFailure(\Closure $closure): static
    {
        $this->executeOnFailure[] = $closure;

        return $this;
    }

    public function codeCoverageIsEnabled(): bool
    {
        return $this->codeCoverage;
    }

    public function enableCodeCoverage(): static
    {
        $this->codeCoverage = $this->adapter->extension_loaded('xdebug');

        return $this;
    }

    public function disableCodeCoverage(): static
    {
        $this->codeCoverage = false;

        return $this;
    }

    public function branchesAndPathsCoverageIsEnabled(): bool
    {
        return $this->branchesAndPathsCoverage;
    }

    public function enableBranchesAndPathsCoverage(): static
    {
        $this->branchesAndPathsCoverage = $this->codeCoverageIsEnabled() && defined('XDEBUG_CC_BRANCH_CHECK');

        return $this;
    }

    public function disableBranchesAndPathsCoverage(): static
    {
        $this->branchesAndPathsCoverage = false;

        return $this;
    }

    public function setMaxChildrenNumber(int $number): static
    {
        if ($number < 1) {
            throw new exceptions\logic\invalidArgument('Maximum number of children must be greater or equal to 1');
        }

        $this->maxAsynchronousEngines = $number;

        return $this;
    }

    public function setBootstrapFile(?string $path): static
    {
        $this->bootstrapFile = $path;

        return $this;
    }

    public function getBootstrapFile(): ?string
    {
        return $this->bootstrapFile;
    }

    public function setAutoloaderFile(?string $path): static
    {
        $this->autoloaderFile = $path;

        return $this;
    }

    public function getAutoloaderFile(): ?string
    {
        return $this->autoloaderFile;
    }

    public function setTestNamespace(string $testNamespace): static
    {
        $testNamespace = self::cleanNamespace($testNamespace);

        if ($testNamespace === '') {
            throw new exceptions\logic\invalidArgument('Test namespace must not be empty');
        }

        if (!$this->analyzer->isRegex($testNamespace) && !$this->analyzer->isValidNamespace($testNamespace)) {
            throw new exceptions\logic\invalidArgument('Test namespace must be a valid regex or identifier');
        }

        $this->testNamespace = $testNamespace;

        return $this;
    }

    public function getTestNamespace(): string
    {
        return $this->testNamespace !== null ? $this->testNamespace : self::getNamespace();
    }

    public function setTestMethodPrefix(string $methodPrefix): static
    {
        if ($methodPrefix === '') {
            throw new exceptions\logic\invalidArgument('Test method prefix must not be empty');
        }

        if (!$this->analyzer->isRegex($methodPrefix) && !$this->analyzer->isValidIdentifier($methodPrefix)) {
            throw new exceptions\logic\invalidArgument('Test method prefix must a valid regex or identifier');
        }

        $this->testMethodPrefix = $methodPrefix;

        return $this;
    }

    public function getTestMethodPrefix(): string
    {
        return $this->testMethodPrefix !== null ? $this->testMethodPrefix : self::getMethodPrefix();
    }

    public function setPhpPath(?string $path): static
    {
        $this->phpPath = $path;

        return $this;
    }

    public function getPhpPath(): ?string
    {
        return $this->phpPath;
    }

    public function getAllTags(): array
    {
        $tags = $this->getTags();

        foreach ($this->testMethods as $annotations) {
            if (isset($annotations['tags']) === true) {
                $tags = array_merge($tags, array_diff($annotations['tags'], $tags));
            }
        }

        return array_values($tags);
    }

    public function setTags(array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setMethodTags(string $testMethodName, array $tags): static
    {
        $this->checkMethod($testMethodName)->testMethods[$testMethodName]['tags'] = $tags;

        return $this;
    }

    public function getMethodTags(?string $testMethodName = null): array
    {
        $tags = [];

        $classTags = $this->getTags();

        if ($testMethodName === null) {
            foreach ($this->testMethods as $testMethodName => $annotations) {
                $tags[$testMethodName] = isset($annotations['tags']) === false ? $classTags : array_values(array_unique(array_merge($classTags, $annotations['tags'])));
            }
        } else {
            $tags = isset($this->checkMethod($testMethodName)->testMethods[$testMethodName]['tags']) === false ? $classTags : array_values(array_unique(array_merge($classTags, $this->testMethods[$testMethodName]['tags'])));
        }

        return $tags;
    }

    public function getDataProviders(): array
    {
        return $this->dataProviders;
    }

    public function getTestedClassName(): ?string
    {
        if ($this->testedClassName === null) {
            $this->testedClassName = self::getTestedClassNameFromTestClass($this->getClass(), $this->getTestNamespace(), $this->getAnalyzer());
        }

        return $this->testedClassName;
    }

    public function getTestedClassNamespace(): string
    {
        $testedClassName = $this->getTestedClassName();

        return substr($testedClassName, 0, strrpos($testedClassName, '\\'));
    }

    public function getTestedClassPath(): ?string
    {
        if ($this->testedClassPath === null) {
            $testedClass = new \reflectionClass($this->getTestedClassName());

            $this->testedClassPath = $testedClass->getFilename();
        }

        return $this->testedClassPath;
    }

    public function setTestedClassName(string $className): static
    {
        if ($this->testedClassName !== null) {
            throw new exceptions\runtime('Tested class name is already defined');
        }

        $this->testedClassName = $className;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getClassNamespace(): string
    {
        return $this->classNamespace;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getTaggedTestMethods(array $methods, array $tags = []): array
    {
        return array_values(array_uintersect($methods, $this->getTestMethods($tags), 'strcasecmp'));
    }

    public function getTestMethods(array $tags = []): array
    {
        $testMethods = [];

        foreach (array_keys($this->testMethods) as $methodName) {
            if ($this->methodIsIgnored($methodName, $tags) === false) {
                $testMethods[] = $methodName;
            }
        }

        return $testMethods;
    }

    public function getCurrentMethod(): ?string
    {
        return $this->currentMethod;
    }

    public function getMaxChildrenNumber(): ?int
    {
        return $this->maxAsynchronousEngines;
    }

    public function getCoverage(): score\coverage
    {
        return $this->score->getCoverage();
    }

    #[\ReturnTypeWillChange]
    public function count(): int
    {
        return count($this->runTestMethods);
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

    public function getObservers(): array
    {
        return iterator_to_array($this->observers);
    }

    public function callObservers(string $event): void
    {
        foreach ($this->observers as $observer) {
            $observer->handleEvent($event, $this);
        }
    }

    public function ignore(bool $boolean): static
    {
        $this->ignore = $boolean;

        return $this->runTestMethods($this->getTestMethods());
    }

    public function isIgnored(array $namespaces = [], array $tags = []): bool
    {
        $isIgnored = (count($this) <= 0 || $this->ignore === true);

        if ($isIgnored === false && count($namespaces) > 0) {
            $classNamespace = strtolower($this->getClassNamespace());

            $isIgnored = count(array_filter($namespaces, function ($value) use ($classNamespace) {
                return strpos($classNamespace, strtolower($value)) === 0;
            })) <= 0;
        }

        if ($isIgnored === false && count($tags) > 0) {
            $isIgnored = count($testTags = $this->getAllTags()) <= 0 || count(array_intersect($tags, $testTags)) == 0;
        }

        return $isIgnored;
    }

    public function ignoreMethod(string $methodName, bool $boolean): static
    {
        $this->checkMethod($methodName)->testMethods[$methodName]['ignore'] = $boolean;

        return $this->runTestMethods($this->getTestMethods());
    }

    public function methodIsIgnored(string $methodName, array $tags = []): bool
    {
        $isIgnored = $this->checkMethod($methodName)->ignore;

        if ($isIgnored === false) {
            if (isset($this->testMethods[$methodName]['ignore']) === true) {
                $isIgnored = $this->testMethods[$methodName]['ignore'];
            }

            if ($isIgnored === false && $tags) {
                $isIgnored = count($methodTags = $this->getMethodTags($methodName)) <= 0 || count(array_intersect($tags, $methodTags)) <= 0;
            }
        }

        return $isIgnored;
    }

    public function runTestMethods(array $methods, array $tags = []): static
    {
        $this->runTestMethods = $runTestMethods = [];

        if (isset($methods['*']) === true) {
            $runTestMethods = $methods['*'];
        }

        $testClass = $this->getClass();

        if (isset($methods[$testClass]) === true) {
            $runTestMethods = $methods[$testClass];
        }

        if (in_array('*', $runTestMethods) === true) {
            $runTestMethods = [];
        }

        if (count($runTestMethods) <= 0) {
            $runTestMethods = $this->getTestMethods($tags);
        } else {
            $runTestMethods = $this->getTaggedTestMethods($runTestMethods, $tags);
        }

        foreach ($runTestMethods as $method) {
            $method = (string) $method;

            if ($method === '') {
                continue;
            }

            if ($this->xdebugConfig != null) {
                $engineClass = 'atoum\atoum\test\engines\concurrent';
            } else {
                $engineName = $engineClass = ($this->getMethodEngine($method) ?: $this->getClassEngine() ?: self::getDefaultEngine());

                if (substr($engineClass, 0, 1) !== '\\') {
                    $engineClass = self::enginesNamespace . '\\' . $engineClass;
                }

                if (class_exists($engineClass) === false) {
                    throw new exceptions\runtime('Test engine \'' . $engineName . '\' does not exist for method \'' . $this->class . '::' . $method . '()\'');
                }
            }

            $engine = new $engineClass();

            if ($engine instanceof test\engine === false) {
                throw new exceptions\runtime('Test engine \'' . $engineName . '\' is invalid for method \'' . $this->class . '::' . $method . '()\'');
            }

            $this->runTestMethods[$method] = $engine;
        }

        return $this;
    }

    public function runTestMethod(string $testMethod, array $tags = []): static
    {
        if ($this->methodIsIgnored($testMethod, $tags) === false) {
            $this->mockAutoloader->setMockGenerator($this->mockGenerator)->register();

            set_error_handler([$this, 'errorHandler']);

            ini_set('display_errors', 'stderr');
            ini_set('log_errors', 'Off');
            ini_set('log_errors_max_len', '0');

            $this->currentMethod = $testMethod;
            $this->executeOnFailure = [];

            $this->phpFunctionMocker->setDefaultNamespace($this->getTestedClassNamespace());
            $this->phpConstantMocker->setDefaultNamespace($this->getTestedClassNamespace());

            try {
                $os = $this->getMethodSupportedOs($testMethod);
                $supportedOs = array_filter(
                    $os,
                    function ($os) {
                        return $os[0] !== '!';
                    }
                );
                $unsupportedOs = array_map(
                    function ($os) {
                        return substr($os, 1);
                    },
                    array_filter(
                        $os,
                        function ($os) {
                            return $os[0] === '!';
                        }
                    )
                );

                if (count($supportedOs) > 0 && in_array(strtolower(PHP_OS), $supportedOs) === false) {
                    throw new test\exceptions\skip(PHP_OS . ' OS is not supported');
                }

                if (count($unsupportedOs) > 0 && in_array(strtolower(PHP_OS), $unsupportedOs) === true) {
                    throw new test\exceptions\skip(PHP_OS . ' OS is not supported');
                }

                foreach ($this->getMethodPhpVersions($testMethod) as $phpVersion => $operator) {
                    if (version_compare(phpversion(), $phpVersion, $operator) === false) {
                        throw new test\exceptions\skip('PHP version ' . PHP_VERSION . ' is not ' . $operator . ' to ' . $phpVersion);
                    }
                }

                foreach ($this->getMandatoryMethodExtensions($testMethod) as $mandatoryExtension) {
                    try {
                        call_user_func($this->phpExtensionFactory, $mandatoryExtension)->requireExtension();
                    } catch (php\exception $exception) {
                        throw new test\exceptions\skip($exception->getMessage());
                    }
                }

                $testException = null;
                try {
                    ob_start();

                    test\adapter::setStorage($this->testAdapterStorage);
                    mock\controller::setLinker($this->mockControllerLinker);

                    $this->testAdapterStorage->add(php\mocker::getAdapter());

                    $this->callBeforeTestMethod($this->currentMethod);

                    $this->mockGenerator->testedClassIs($this->getTestedClassName());

                    try {
                        $testedClass = new \reflectionClass($testedClassName = $this->getTestedClassName());
                    } catch (\exception $exception) {
                        throw new exceptions\runtime('Tested class \'' . $testedClassName . '\' does not exist for test class \'' . $this->getClass() . '\'');
                    }

                    if ($testedClass->isAbstract() === true) {
                        $testedClass = new \reflectionClass($testedClassName = $this->mockGenerator->getDefaultNamespace() . '\\' . $testedClassName);
                    }

                    $this->factoryBuilder->build($testedClass, $instance)
                        ->addToAssertionManager(
                            $this->assertionManager,
                            'newTestedInstance',
                            function () use ($testedClass) {
                                throw new exceptions\runtime('Tested class ' . $testedClass->getName() . ' has no constructor or its constructor has at least one mandatory argument');
                            }
                        );

                    $this->factoryBuilder->build($testedClass)
                        ->addToAssertionManager(
                            $this->assertionManager,
                            'newInstance',
                            function () use ($testedClass) {
                                throw new exceptions\runtime('Tested class ' . $testedClass->getName() . ' has no constructor or its constructor has at least one mandatory argument');
                            }
                        );

                    $this->assertionManager->setPropertyHandler(
                        'testedInstance',
                        function () use (& $instance) {
                            if ($instance === null) {
                                throw new exceptions\runtime('Use $this->newTestedInstance before using $this->testedInstance');
                            }

                            return $instance;
                        }
                    );

                    if ($this->codeCoverageIsEnabled() === true) {
                        $options = XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE;

                        if ($this->branchesAndPathsCoverageIsEnabled() === true) {
                            $options |= XDEBUG_CC_BRANCH_CHECK;
                        }

                        xdebug_start_code_coverage($options);
                    }

                    $assertionNumber = $this->score->getAssertionNumber();
                    $time = microtime(true);
                    $memory = memory_get_peak_usage();

                    if (isset($this->dataProviders[$testMethod]) === false) {
                        $this->{$testMethod}();

                        $this->asserterCallManager->check();
                    } else {
                        $dataProvider = $this->dataProviders[$testMethod];

                        if ($dataProvider instanceof test\data\provider) {
                            $data = $this->dataProviders[$testMethod]();
                        } else {
                            $data = $this->{$this->dataProviders[$testMethod]}();
                        }

                        if (is_array($data) === false && $data instanceof \traversable === false) {
                            throw new test\exceptions\runtime('Data provider ' . $this->getClass() . '::' . $this->dataProviders[$testMethod] . '() must return an array or an iterator');
                        }

                        $reflectedTestMethod = call_user_func($this->reflectionMethodFactory, $this, $testMethod);
                        $numberOfArguments = $reflectedTestMethod->getNumberOfRequiredParameters();

                        foreach ($data as $key => $arguments) {
                            if (is_array($arguments) === false) {
                                $arguments = [$arguments];
                            }

                            if (count($arguments) < $numberOfArguments) {
                                throw new test\exceptions\runtime('Data provider ' . ($dataProvider instanceof test\data\provider ? '' : $this->getClass() . '::' . $this->dataProviders[$testMethod] . '() ') . 'does not provide enough arguments at key ' . $key . ' for test method ' . $this->getClass() . '::' . $testMethod . '()');
                            }

                            $this->score->setDataSet($key, $this->dataProviders[$testMethod]);

                            $reflectedTestMethod->invokeArgs($this, $arguments);

                            $this->asserterCallManager->check();

                            $this->score->unsetDataSet();
                        }
                    }

                    $this->mockControllerLinker->reset();
                    $this->testAdapterStorage->reset();

                    $memoryUsage = memory_get_peak_usage() - $memory;
                    $duration = microtime(true) - $time;

                    $this->score
                        ->addMemoryUsage($this->path, $this->class, $this->currentMethod, $memoryUsage)
                        ->addDuration($this->path, $this->class, $this->currentMethod, $duration)
                        ->addOutput($this->path, $this->class, $this->currentMethod, ob_get_clean());

                    if ($this->codeCoverageIsEnabled() === true) {
                        $this->score->getCoverage()->addXdebugDataForTest($this, xdebug_get_code_coverage());
                        xdebug_stop_code_coverage();
                    }

                    if ($assertionNumber == $this->score->getAssertionNumber() && $this->methodIsNotVoid($this->currentMethod) === false) {
                        $this->score->addVoidMethod($this->path, $this->class, $this->currentMethod);
                    }
                } catch (\exception $exception) {
                    $this->score->addOutput($this->path, $this->class, $this->currentMethod, ob_get_clean());

                    $testException = $exception;
                } finally {
                    $afterTestException = null;
                    try {
                        ob_start();
                        $this->callAfterTestMethod($this->currentMethod);
                        $this->score->addOutput($this->path, $this->class, $this->currentMethod, ob_get_clean());
                    } catch (\exception $exception) {
                        $afterTestException = $exception;
                    }

                    if ($testException !== null) {
                        throw $testException;
                    } elseif ($afterTestException !== null) {
                        throw $afterTestException;
                    }
                }
            } catch (asserter\exception $exception) {
                foreach ($this->executeOnFailure as $closure) {
                    ob_start();
                    $closure();
                    $this->score->addOutput($this->path, $this->class, $this->currentMethod, ob_get_clean());
                }

                if ($this->score->failExists($exception) === false) {
                    $this->addExceptionToScore($exception);
                }
            } catch (test\exceptions\runtime $exception) {
                $this->score->addRuntimeException($this->path, $this->class, $this->currentMethod, $exception);
            } catch (test\exceptions\skip $exception) {
                list($file, $line) = $this->getBacktrace($exception->getTrace());

                $this->score->addSkippedMethod($file, $this->class, $this->currentMethod, $line, $exception->getMessage());
            } catch (test\exceptions\stop $exception) {
            } catch (exception $exception) {
                list($file, $line) = $this->getBacktrace($exception->getTrace());

                $this->errorHandler(E_USER_ERROR, $exception->getMessage(), $file, $line);
            } catch (\exception $exception) {
                $this->addExceptionToScore($exception);
            }

            $this->currentMethod = null;

            restore_error_handler();

            ini_restore('display_errors');
            ini_restore('log_errors');
            ini_restore('log_errors_max_len');

            $this->mockAutoloader->unregister();
        }

        return $this;
    }

    public function run(array $runTestMethods = [], array $tags = [])
    {
        if ($runTestMethods) {
            $this->runTestMethods(array_intersect($runTestMethods, $this->getTestMethods($tags)));
        }

        if ($this->isIgnored() === false) {
            $this->callObservers(self::runStart);

            try {
                $this->runEngines();
            } catch (\exception $exception) {
                $this->stopEngines();

                throw $exception;
            }

            $this->callObservers(self::runStop);
        }

        return $this;
    }

    public function startCase($case)
    {
        $this->testAdapterStorage->resetCalls();
        $this->score->setCase($case);

        return $this;
    }

    public function stopCase()
    {
        $this->testAdapterStorage->resetCalls();
        $this->score->unsetCase();

        return $this;
    }

    public function setDataProvider($testMethodName, $dataProvider = null)
    {
        if ($dataProvider === null) {
            $dataProvider = $testMethodName . 'DataProvider';

            if (method_exists($this->checkMethod($testMethodName), $dataProvider) === false) {
                $reflectedMethod = call_user_func($this->reflectionMethodFactory, $this, $testMethodName);
                $parametersProvider = new test\data\provider\aggregator();

                foreach ($reflectedMethod->getParameters() as $parameter) {
                    $parameterProvider = new test\data\providers\mock($this->mockGenerator);

                    $parameterClassName = $parameter->hasType() && !$parameter->getType()->isBuiltin()
                        ? $parameter->getType()->getName()
                        : null;

                    if ($parameterClassName === null) {
                        throw new exceptions\logic\invalidArgument('Could not generate a data provider for ' . $this->class . '::' . $testMethodName . '() because it has at least one argument which is not type-hinted with a class or interface name');
                    }

                    $parametersProvider->addProvider($parameterProvider->setClass($parameterClassName));
                }

                $dataProvider = new test\data\set($parametersProvider);
            }
        }

        if ($dataProvider instanceof \Closure) {
            throw new exceptions\logic\invalidArgument('Cannot use a closure as a data provider for method ' . $this->class . '::' . $testMethodName . '()');
        }

        if ($dataProvider instanceof test\data\provider === false && method_exists($this->checkMethod($testMethodName), $dataProvider) === false) {
            throw new exceptions\logic\invalidArgument('Data provider ' . $this->class . '::' . lcfirst($dataProvider) . '() is unknown');
        }

        $this->dataProviders[$testMethodName] = $dataProvider;

        return $this;
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $doNotCallDefaultErrorHandler = true;
        $errorReporting = $this->adapter->error_reporting();

        if ($errorReporting & $errno) {
            list($file, $line) = $this->getBacktrace();
            $resolvedFile = $file ?: ($errfile ?: $this->path);
            $resolvedLine = $line ?? $errline ?? 0;

            $this->score->addError($resolvedFile, $this->class, $this->currentMethod, $resolvedLine, $errno, trim($errstr), $errfile, $errline);

            $doNotCallDefaultErrorHandler = !($errno & E_RECOVERABLE_ERROR);
        }

        return $doNotCallDefaultErrorHandler;
    }

    protected function callSetUp()
    {
        if (method_exists($this, 'setUp')) {
            ob_start();
            $this->setUp();
            $this->score
                ->addOutput($this->path, $this->class, 'setUp', ob_get_clean());
        }

        return $this;
    }

    protected function callTearDown()
    {
        if (method_exists($this, 'tearDown')) {
            ob_start();
            $this->tearDown();
            $this->score
                ->addOutput($this->path, $this->class, 'tearDown', ob_get_clean());
        }

        return $this;
    }

    protected function callBeforeTestMethod($testMethod)
    {
        if (method_exists($this, 'beforeTestMethod')) {
            $this->beforeTestMethod($testMethod);
        }

        return $this;
    }

    protected function callAfterTestMethod($testMethod)
    {
        if (method_exists($this, 'afterTestMethod')) {
            $this->afterTestMethod($testMethod);
        }

        return $this;
    }

    public static function setNamespace($namespace)
    {
        $namespace = self::cleanNamespace($namespace);

        if ($namespace === '') {
            throw new exceptions\logic\invalidArgument('Namespace must not be empty');
        }

        self::$namespace = $namespace;
    }

    public static function getNamespace()
    {
        return self::$namespace ?: static::defaultNamespace;
    }

    public static function setMethodPrefix($methodPrefix)
    {
        if ($methodPrefix == '') {
            throw new exceptions\logic\invalidArgument('Method prefix must not be empty');
        }

        self::$methodPrefix = $methodPrefix;
    }

    public static function getMethodPrefix()
    {
        return self::$methodPrefix ?: static::defaultMethodPrefix;
    }

    public static function setDefaultEngine($defaultEngine)
    {
        self::$defaultEngine = (string) $defaultEngine;
    }

    public static function getDefaultEngine()
    {
        return self::$defaultEngine ?: self::defaultEngine;
    }

    public static function getTestedClassNameFromTestClass($fullyQualifiedClassName, $testNamespace = null, ?analyzer $analyzer = null)
    {
        $analyzer = $analyzer ?: new analyzer();

        if ($testNamespace === null) {
            $testNamespace = self::getNamespace();
        }

        if ($analyzer->isRegex($testNamespace) === true) {
            if (preg_match($testNamespace, $fullyQualifiedClassName) === 0) {
                throw new exceptions\runtime('Test class \'' . $fullyQualifiedClassName . '\' is not in a namespace which matches pattern \'' . $testNamespace . '\'');
            }

            $testedClassName = preg_replace($testNamespace, '\\', $fullyQualifiedClassName);
        } else {
            $position = strpos($fullyQualifiedClassName, $testNamespace);

            if ($position === false) {
                throw new exceptions\runtime('Test class \'' . $fullyQualifiedClassName . '\' is not in a namespace which contains \'' . $testNamespace . '\'');
            }

            $testedClassName = substr($fullyQualifiedClassName, 0, $position) . substr($fullyQualifiedClassName, $position + 1 + strlen($testNamespace));
        }

        return trim($testedClassName, '\\');
    }

    protected function applyClassAttributes(\ReflectionClass $class): void
    {
        foreach ($class->getAttributes(attributes\Php::class) as $attribute) {
            $php = $attribute->newInstance();
            $this->addClassPhpVersion($php->version, $php->operator);
        }

        foreach ($class->getAttributes(attributes\Ignore::class) as $attribute) {
            $this->ignore($attribute->newInstance()->value);
        }

        $classTags = [];
        foreach ($class->getAttributes(attributes\Tags::class) as $attribute) {
            $classTags = array_merge($classTags, $attribute->newInstance()->tags);
        }
        if ($classTags !== []) {
            $this->setTags($classTags);
        }

        foreach ($class->getAttributes(attributes\TestNamespace::class) as $attribute) {
            $value = $attribute->newInstance()->value;
            $this->setTestNamespace($value ?? static::defaultNamespace);
        }

        foreach ($class->getAttributes(attributes\TestMethodPrefix::class) as $attribute) {
            $this->setTestMethodPrefix($attribute->newInstance()->value);
        }

        foreach ($class->getAttributes(attributes\MaxChildrenNumber::class) as $attribute) {
            $this->setMaxChildrenNumber($attribute->newInstance()->value);
        }

        foreach ($class->getAttributes(attributes\Engine::class) as $attribute) {
            $this->setClassEngine($attribute->newInstance()->value);
        }

        foreach ($class->getAttributes(attributes\Extensions::class) as $attribute) {
            foreach ($attribute->newInstance()->extensions as $extension) {
                $this->addMandatoryClassExtension($extension);
            }
        }

        foreach ($class->getAttributes(attributes\Os::class) as $attribute) {
            foreach ($attribute->newInstance()->os as $os) {
                $this->addClassSupportedOs($os);
            }
        }

        if ($class->getAttributes(attributes\HasVoidMethods::class) !== []) {
            $this->classHasVoidMethods();
        }

        if ($class->getAttributes(attributes\HasNotVoidMethods::class) !== []) {
            $this->classHasNotVoidMethods();
        }
    }

    protected function applyMethodAttributes(\ReflectionMethod $method): void
    {
        $methodName = $method->getName();

        foreach ($method->getAttributes(attributes\Php::class) as $attribute) {
            $php = $attribute->newInstance();
            $this->addMethodPhpVersion($methodName, $php->version, $php->operator);
        }

        foreach ($method->getAttributes(attributes\Ignore::class) as $attribute) {
            $this->ignoreMethod($methodName, $attribute->newInstance()->value);
        }

        $methodTags = [];
        foreach ($method->getAttributes(attributes\Tags::class) as $attribute) {
            $methodTags = array_merge($methodTags, $attribute->newInstance()->tags);
        }
        if ($methodTags !== []) {
            $this->setMethodTags($methodName, $methodTags);
        }

        foreach ($method->getAttributes(attributes\DataProvider::class) as $attribute) {
            $this->setDataProvider($methodName, $attribute->newInstance()->value);
        }

        foreach ($method->getAttributes(attributes\Engine::class) as $attribute) {
            $this->setMethodEngine($methodName, $attribute->newInstance()->value);
        }

        if ($method->getAttributes(attributes\IsVoid::class) !== []) {
            $this->setMethodVoid($methodName);
        }

        if ($method->getAttributes(attributes\IsNotVoid::class) !== []) {
            $this->setMethodNotVoid($methodName);
        }

        foreach ($method->getAttributes(attributes\Extensions::class) as $attribute) {
            foreach ($attribute->newInstance()->extensions as $extension) {
                $this->addMandatoryMethodExtension($methodName, $extension);
            }
        }

        foreach ($method->getAttributes(attributes\Os::class) as $attribute) {
            foreach ($attribute->newInstance()->os as $os) {
                $this->addMethodSupportedOs($methodName, $os);
            }
        }
    }

    protected function setClassAnnotations(annotations\extractor $extractor)
    {
        $extractor
            ->resetHandlers()
            ->setHandler('ignore', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @ignore annotation on %s is deprecated; use #[\atoum\atoum\attributes\Ignore] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                $this->ignore(annotations\extractor::toBoolean($value));
            })
            ->setHandler('tags', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @tags annotation on %s is deprecated; use #[\atoum\atoum\attributes\Tags] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                $this->setTags(annotations\extractor::toArray($value));
            })
            ->setHandler('namespace', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @namespace annotation on %s is deprecated; use #[\atoum\atoum\attributes\TestNamespace] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                $this->setTestNamespace($value === true ? static::defaultNamespace : $value);
            })
            ->setHandler('methodPrefix', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @methodPrefix annotation on %s is deprecated; use #[\atoum\atoum\attributes\TestMethodPrefix] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                $this->setTestMethodPrefix($value === true ? static::defaultMethodPrefix : $value);
            })
            ->setHandler('maxChildrenNumber', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @maxChildrenNumber annotation on %s is deprecated; use #[\atoum\atoum\attributes\MaxChildrenNumber] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                $this->setMaxChildrenNumber($value);
            })
            ->setHandler('engine', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @engine annotation on %s is deprecated; use #[\atoum\atoum\attributes\Engine] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                $this->setClassEngine($value);
            })
            ->setHandler('hasVoidMethods', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @hasVoidMethods annotation on %s is deprecated; use #[\atoum\atoum\attributes\HasVoidMethods] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                $this->classHasVoidMethods();
            })
            ->setHandler('hasNotVoidMethods', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @hasNotVoidMethods annotation on %s is deprecated; use #[\atoum\atoum\attributes\HasNotVoidMethods] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                $this->classHasNotVoidMethods();
            })
            ->setHandler(
                'php',
                function ($value) {
                    @trigger_error(
                        sprintf(
                            'Using @php annotation on %s is deprecated; use #[\atoum\atoum\attributes\Php] instead.',
                            $this->class
                        ),
                        E_USER_DEPRECATED
                    );

                    $value = annotations\extractor::toArray($value);

                    if (isset($value[0]) === true) {
                        $operator = null;

                        if (isset($value[1]) === false) {
                            $version = $value[0];
                        } else {
                            $version = $value[1];

                            switch ($value[0]) {
                                case '<':
                                case '<=':
                                case '=':
                                case '==':
                                case '>=':
                                case '>':
                                    $operator = $value[0];
                            }
                        }

                        $this->addClassPhpVersion($version, $operator);
                    }
                }
            )
            ->setHandler(
                'extensions',
                function ($value) {
                    @trigger_error(
                        sprintf(
                            'Using @extensions annotation on %s is deprecated; use #[\atoum\atoum\attributes\Extensions] instead.',
                            $this->class
                        ),
                        E_USER_DEPRECATED
                    );
                    foreach (annotations\extractor::toArray($value) as $mandatoryExtension) {
                        $this->addMandatoryClassExtension($mandatoryExtension);
                    }
                }
            )
            ->setHandler('os', function ($value) {
                @trigger_error(
                    sprintf(
                        'Using @os annotation on %s is deprecated; use #[\atoum\atoum\attributes\Os] instead.',
                        $this->class
                    ),
                    E_USER_DEPRECATED
                );
                foreach (annotations\extractor::toArray($value) as $supportedOs) {
                    $this->addClassSupportedOs($supportedOs);
                }
            });

        return $this;
    }

    protected function setMethodAnnotations(annotations\extractor $extractor, & $methodName)
    {
        $extractor
            ->resetHandlers()
            ->setHandler('ignore', function ($value) use (& $methodName) {
                @trigger_error(
                    sprintf(
                        'Using @ignore annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\Ignore] instead.',
                        $this->class,
                        $methodName
                    ),
                    E_USER_DEPRECATED
                );
                $this->ignoreMethod($methodName, annotations\extractor::toBoolean($value));
            })
            ->setHandler('tags', function ($value) use (& $methodName) {
                @trigger_error(
                    sprintf(
                        'Using @tags annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\Tags] instead.',
                        $this->class,
                        $methodName
                    ),
                    E_USER_DEPRECATED
                );
                $this->setMethodTags($methodName, annotations\extractor::toArray($value));
            })
            ->setHandler('dataProvider', function ($value) use (& $methodName) {
                @trigger_error(
                    sprintf(
                        'Using @dataProvider annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\DataProvider] instead.',
                        $this->class,
                        $methodName
                    ),
                    E_USER_DEPRECATED
                );
                $this->setDataProvider($methodName, $value === true ? null : $value);
            })
            ->setHandler('engine', function ($value) use (& $methodName) {
                @trigger_error(
                    sprintf(
                        'Using @engine annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\Engine] instead.',
                        $this->class,
                        $methodName
                    ),
                    E_USER_DEPRECATED
                );
                $this->setMethodEngine($methodName, $value);
            })
            ->setHandler('isVoid', function ($value) use (& $methodName) {
                @trigger_error(
                    sprintf(
                        'Using @isVoid annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\IsVoid] instead.',
                        $this->class,
                        $methodName
                    ),
                    E_USER_DEPRECATED
                );
                $this->setMethodVoid($methodName);
            })
            ->setHandler('isNotVoid', function ($value) use (& $methodName) {
                @trigger_error(
                    sprintf(
                        'Using @isNotVoid annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\IsNotVoid] instead.',
                        $this->class,
                        $methodName
                    ),
                    E_USER_DEPRECATED
                );
                $this->setMethodNotVoid($methodName);
            })
            ->setHandler(
                'php',
                function ($value) use (& $methodName) {
                    @trigger_error(
                        sprintf(
                            'Using @php annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\Php] instead.',
                            $this->class,
                            $methodName
                        ),
                        E_USER_DEPRECATED
                    );

                    $value = annotations\extractor::toArray($value);

                    if (isset($value[0]) === true) {
                        $operator = null;

                        if (isset($value[1]) === false) {
                            $version = $value[0];
                        } else {
                            $version = $value[1];

                            switch ($value[0]) {
                                case '<':
                                case '<=':
                                case '=':
                                case '==':
                                case '>=':
                                case '>':
                                    $operator = $value[0];
                            }
                        }

                        $this->addMethodPhpVersion($methodName, $version, $operator);
                    }
                }
            )
            ->setHandler(
                'extensions',
                function ($value) use (& $methodName) {
                    @trigger_error(
                        sprintf(
                            'Using @extensions annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\Extensions] instead.',
                            $this->class,
                            $methodName
                        ),
                        E_USER_DEPRECATED
                    );
                    foreach (annotations\extractor::toArray($value) as $mandatoryExtension) {
                        $this->addMandatoryMethodExtension($methodName, $mandatoryExtension);
                    }
                }
            )
            ->setHandler('os', function ($value) use (& $methodName) {
                @trigger_error(
                    sprintf(
                        'Using @os annotation on %s::%s() is deprecated; use #[\atoum\atoum\attributes\Os] instead.',
                        $this->class,
                        $methodName
                    ),
                    E_USER_DEPRECATED
                );
                foreach (annotations\extractor::toArray($value) as $supportedOs) {
                    $this->addMethodSupportedOs($methodName, $supportedOs);
                }
            });

        return $this;
    }

    protected function getBacktrace(?array $trace = null)
    {
        $debugBacktrace = $trace === null ? debug_backtrace(false) : $trace;

        foreach ($debugBacktrace as $key => $value) {
            if (isset($value['class']) === true && $value['class'] === $this->class && isset($value['function']) === true && $value['function'] === $this->currentMethod) {
                if (isset($debugBacktrace[$key - 1]) === true) {
                    $key -= 1;
                }

                return [
                    $debugBacktrace[$key]['file'],
                    $debugBacktrace[$key]['line']
                ];
            }
        }

        return null;
    }

    private function checkMethod(?string $methodName): static
    {
        if ($methodName === null || isset($this->testMethods[$methodName]) === false) {
            throw new exceptions\logic\invalidArgument('Test method ' . $this->class . '::' . $methodName . '() does not exist');
        }

        return $this;
    }

    private function addExceptionToScore(\exception $exception)
    {
        list($file, $line) = $this->getBacktrace($exception->getTrace());

        $this->score->addException($file, $this->class, $this->currentMethod, $line, $exception);

        return $this;
    }

    private function runEngines()
    {
        $this->callObservers(self::beforeSetUp);
        $this->callSetUp();
        $this->callObservers(self::afterSetUp);

        while ($this->runEngine()->engines) {
            $engines = $this->engines;

            foreach ($engines as $this->currentMethod => $engine) {
                $score = $engine->getScore();

                if ($score !== null) {
                    unset($this->engines[$this->currentMethod]);

                    $this->callObservers(self::afterTestMethod);
                    $this->score->merge($score);

                    $runtimeExceptions = $score->getRuntimeExceptions();

                    if (count($runtimeExceptions) > 0) {
                        $this->callObservers(self::runtimeException);

                        throw reset($runtimeExceptions);
                    } else {
                        switch (true) {
                            case $score->getVoidMethodNumber():
                                $signal = self::void;
                                break;

                            case $score->getUncompletedMethodNumber():
                                $signal = self::uncompleted;
                                break;

                            case $score->getSkippedMethodNumber():
                                $signal = self::skipped;
                                break;

                            case $score->getFailNumber():
                                $signal = self::fail;
                                break;

                            case $score->getErrorNumber():
                                $signal = self::error;
                                break;

                            case $score->getExceptionNumber():
                                $signal = self::exception;
                                break;

                            default:
                                $signal = self::success;
                        }

                        $this->callObservers($signal);
                    }

                    if ($engine->isAsynchronous() === true) {
                        $this->asynchronousEngines--;
                    }
                }
            }

            $this->currentMethod = null;
        }

        return $this->doTearDown();
    }

    private function stopEngines()
    {
        while ($this->engines) {
            $engines = $this->engines;

            foreach ($engines as $currentMethod => $engine) {
                if ($engine->getScore() !== null) {
                    unset($this->engines[$currentMethod]);
                }
            }
        }

        return $this->doTearDown();
    }

    private function runEngine()
    {
        $method = array_key_first($this->runTestMethods);

        if ($method !== null) {
            $engine = $this->runTestMethods[$method];

            if ($this->canRunEngine($engine) === true) {
                unset($this->runTestMethods[$method]);

                $this->currentMethod = $method;

                $this->callObservers(self::beforeTestMethod);
                $engine->run($this);
                $this->engines[$method] = $engine;

                if ($engine->isAsynchronous() === true) {
                    $this->asynchronousEngines++;
                }
            }

            $this->currentMethod = null;
        }

        return $this;
    }

    private function canRunEngine(test\engine $engine)
    {
        return ($engine->isAsynchronous() === false || $this->maxAsynchronousEngines === null || $this->asynchronousEngines < $this->maxAsynchronousEngines);
    }

    private function doTearDown()
    {
        $this->callObservers(self::beforeTearDown);
        $this->callTearDown();
        $this->callObservers(self::afterTearDown);

        return $this;
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function removeExtension(extension $extension)
    {
        $this->extensions->offsetUnset($extension);

        return $this->removeObserver($extension);
    }

    public function removeExtensions()
    {
        foreach ($this->extensions as $extension) {
            $this->removeObserver($extension);
        }

        $this->extensions = new \splObjectStorage();

        return $this;
    }

    public function addExtension(extension $extension, ?extension\configuration $configuration = null)
    {
        if ($this->extensions->offsetExists($extension) === false) {
            $this->extensions->offsetUnset($extension);
            $this->removeObserver($extension);
        }

        $this->extensions->offsetSet($extension, $configuration);

        $extension->setTest($this);

        $this->addObserver($extension);

        return $this;
    }

    public function addExtensions(\splObjectStorage $extensions)
    {
        foreach ($extensions as $extension) {
            $this->addExtension($extension, $extensions[$extension]);
        }

        return $this;
    }

    public function getExtensionConfiguration(extension $extension)
    {
        try {
            return $this->extensions[$extension];
        } catch (\unexpectedValueException $e) {
            return null;
        }
    }

    private static function cleanNamespace($namespace)
    {
        return trim((string) $namespace, '\\');
    }
}
