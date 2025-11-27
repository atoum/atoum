<?php

namespace atoum\atoum\scripts;

use atoum\atoum;
use atoum\atoum\exceptions;

class builder extends atoum\script\configurable
{
    public const defaultConfigFile = '.builder.php';
    public const defaultUnitTestRunnerScript = 'scripts/runner.php';
    public const defaultPharGeneratorScript = 'scripts/phar/generator.php';

    private mixed $lockResource = null;

    protected ?atoum\php $php = null;
    protected ?builder\vcs $vcs = null;
    protected ?atoum\scripts\tagger\engine $taggerEngine = null;
    protected ?string $revision = null;
    protected ?string $version = null;
    protected ?string $unitTestRunnerScript = null;
    protected ?string $pharGeneratorScript = null;
    protected ?string $workingDirectory = null;
    protected ?string $destinationDirectory = null;
    protected ?string $scoreDirectory = null;
    protected ?string $errorsDirectory = null;
    protected ?string $revisionFile = null;
    protected ?string $runFile = null;
    protected bool $pharCreationEnabled = true;
    protected bool $checkUnitTests = true;
    protected ?string $reportTitle = null;
    protected array $runnerConfigurationFiles = [];

    public function __construct(string $name, ?atoum\adapter $adapter = null)
    {
        parent::__construct($name, $adapter);

        $this
            ->setVcs()
            ->setPhp()
            ->setUnitTestRunnerScript(self::defaultUnitTestRunnerScript)
            ->setPharGeneratorScript(self::defaultPharGeneratorScript)
        ;
    }

    public function setVcs(?builder\vcs $vcs = null): static
    {
        $this->vcs = $vcs ?: new builder\vcs\svn();

        return $this;
    }

    public function getVcs(): builder\vcs
    {
        return $this->vcs;
    }

    public function setTaggerEngine(atoum\scripts\tagger\engine $engine): static
    {
        $this->taggerEngine = $engine;

        return $this;
    }

    public function getTaggerEngine(): ?atoum\scripts\tagger\engine
    {
        return $this->taggerEngine;
    }

    public function setPhp(?atoum\php $php = null): static
    {
        $this->php = $php ?: new atoum\php();

        return $this;
    }

    public function getPhp(): atoum\php
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

    public function getRunnerConfigurationFiles(): array
    {
        return $this->runnerConfigurationFiles;
    }

    public function addRunnerConfigurationFile(string $file): static
    {
        $this->runnerConfigurationFiles[] = (string) $file;

        return $this;
    }

    public function enablePharCreation(): static
    {
        $this->pharCreationEnabled = true;

        return $this;
    }

    public function disablePharCreation(): static
    {
        $this->pharCreationEnabled = false;

        return $this;
    }

    public function pharCreationIsEnabled(): bool
    {
        return $this->pharCreationEnabled;
    }

    public function disableUnitTestChecking(): static
    {
        $this->checkUnitTests = false;

        return $this;
    }

    public function enableUnitTestChecking(): static
    {
        $this->checkUnitTests = true;

        return $this;
    }

    public function unitTestCheckingIsEnabled(): bool
    {
        return $this->checkUnitTests;
    }

    public function setVersion(string $version): static
    {
        $this->version = (string) $version;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setScoreDirectory(string $path): static
    {
        $this->scoreDirectory = $this->cleanDirectoryPath($path);

        return $this;
    }

    public function getScoreDirectory(): ?string
    {
        return $this->scoreDirectory;
    }

    public function setErrorsDirectory(string $path): static
    {
        $this->errorsDirectory = $this->cleanDirectoryPath($path);

        return $this;
    }

    public function getErrorsDirectory(): ?string
    {
        return $this->errorsDirectory;
    }

    public function setDestinationDirectory(string $path): static
    {
        $this->destinationDirectory = $this->cleanDirectoryPath($path);

        return $this;
    }

    public function getDestinationDirectory(): ?string
    {
        return $this->destinationDirectory;
    }

    public function setWorkingDirectory(string $path): static
    {
        $this->workingDirectory = $this->cleanDirectoryPath($path);

        return $this;
    }

    public function getWorkingDirectory(): ?string
    {
        return $this->workingDirectory;
    }

    public function setRevisionFile(string $path): static
    {
        $this->revisionFile = (string) $path;

        return $this;
    }

    public function getRevisionFile(): ?string
    {
        return $this->revisionFile;
    }

    public function setReportTitle(string $title): static
    {
        $this->reportTitle = (string) $title;

        return $this;
    }

    public function getReportTitle(): ?string
    {
        return $this->reportTitle;
    }

    public function setUnitTestRunnerScript(string $path): static
    {
        $this->unitTestRunnerScript = (string) $path;

        return $this;
    }

    public function getUnitTestRunnerScript(): ?string
    {
        return $this->unitTestRunnerScript;
    }

    public function setPharGeneratorScript(string $path): static
    {
        $this->pharGeneratorScript = (string) $path;

        return $this;
    }

    public function getPharGeneratorScript(): ?string
    {
        return $this->pharGeneratorScript;
    }

    public function setRunFile(string $path): static
    {
        $this->runFile = $path;

        return $this;
    }

    public function getRunFile(): string
    {
        return $this->runFile !== null ? $this->runFile : $this->adapter->sys_get_temp_dir() . \DIRECTORY_SEPARATOR . md5(get_class($this));
    }

    public function checkUnitTests(): bool
    {
        $status = true;

        if ($this->checkUnitTests === true) {
            if ($this->workingDirectory === null) {
                throw new exceptions\logic('Unable to check unit tests, working directory is undefined');
            }

            $this->vcs
                ->setWorkingDirectory($this->workingDirectory)
                ->exportRepository()
            ;

            $this->php
                ->reset()
                ->addOption('-f', $this->workingDirectory . \DIRECTORY_SEPARATOR . $this->unitTestRunnerScript)
                ->addArgument('-ncc')
                ->addArgument('-d', $this->workingDirectory . \DIRECTORY_SEPARATOR . 'tests' . \DIRECTORY_SEPARATOR . 'units' . \DIRECTORY_SEPARATOR . 'classes')
                ->addArgument('-p', $this->php->getBinaryPath())
            ;

            $scoreFile = $this->scoreDirectory === null ? $this->adapter->tempnam($this->adapter->sys_get_temp_dir(), '') : $this->scoreDirectory . DIRECTORY_SEPARATOR . $this->vcs->getRevision();

            $this->php->addArgument('-sf', $scoreFile);

            if ($this->reportTitle !== null) {
                $this->php->addArgument('-drt', sprintf($this->reportTitle, '%1$s', '%2$s', '%3$s', $this->vcs->getRevision()));
            }

            foreach ($this->runnerConfigurationFiles as $runnerConfigurationFile) {
                $this->php->addArgument('-c', $runnerConfigurationFile);
            }

            try {
                $exitCode = $this->php->run()->getExitCode();

                if ($exitCode > 0) {
                    switch ($exitCode) {
                        case 126:
                        case 127:
                            throw new exceptions\runtime('Unable to find \'' . $this->php->getBinaryPath() . '\' or it is not executable');

                        default:
                            throw new exceptions\runtime($this->php . ' failed with exit code \'' . $exitCode . '\': ' . $this->php->getStderr());
                    }
                }

                $stdErr = $this->php->getStdErr();

                if ($stdErr != '') {
                    throw new exceptions\runtime($stdErr);
                }

                $score = @$this->adapter->file_get_contents($scoreFile);

                if ($score === false) {
                    throw new exceptions\runtime('Unable to read score from file \'' . $scoreFile . '\'');
                }

                $score = $this->adapter->unserialize($score);

                if ($score === false) {
                    throw new exceptions\runtime('Unable to unserialize score from file \'' . $scoreFile . '\'');
                }

                if ($score instanceof atoum\score === false) {
                    throw new exceptions\runtime('Contents of file \'' . $scoreFile . '\' is not a score');
                }

                $status = $score->getFailNumber() === 0 && $score->getExceptionNumber() === 0 && $score->getErrorNumber() === 0;
            } catch (\exception $exception) {
                $this->writeErrorInErrorsDirectory($exception->getMessage());

                $status = false;
            }

            if ($this->scoreDirectory === null) {
                if ($this->adapter->unlink($scoreFile) === false) {
                    throw new exceptions\runtime('Unable to delete score file \'' . $scoreFile . '\'');
                }
            }
        }

        return $status;
    }

    public function createPhar(?string $version = null): bool
    {
        $pharBuilt = true;

        if ($this->pharCreationEnabled === true) {
            if ($this->destinationDirectory === null) {
                throw new exceptions\logic('Unable to create phar, destination directory is undefined');
            }

            if ($this->workingDirectory === null) {
                throw new exceptions\logic('Unable to create phar, working directory is undefined');
            }

            if ($this->revisionFile !== null) {
                $revision = trim(@$this->adapter->file_get_contents($this->revisionFile));

                if (is_numeric($revision) === true) {
                    $this->vcs->setRevision($revision);
                }
            }

            $revisions = $this->vcs->getNextRevisions();

            while (count($revisions) > 0) {
                $revision = array_shift($revisions);

                $this->vcs->setRevision($revision);

                try {
                    if ($this->checkUnitTests() === true) {
                        if ($this->checkUnitTests === false) {
                            $this->vcs
                                ->setWorkingDirectory($this->workingDirectory)
                                ->exportRepository()
                            ;
                        }

                        if ($this->taggerEngine !== null) {
                            $this->taggerEngine
                                ->setSrcDirectory($this->workingDirectory)
                                ->setVersion($version !== null ? $version : 'nightly-' . $revision . '-' . $this->adapter->date('YmdHi'))
                                ->tagVersion()
                            ;
                        }

                        $this->php
                            ->reset()
                            ->addOption('-d', 'phar.readonly=0')
                            ->addOption('-f', $this->workingDirectory . DIRECTORY_SEPARATOR . $this->pharGeneratorScript)
                            ->addArgument('-d', $this->destinationDirectory)
                        ;

                        if ($this->php->run()->getExitCode() > 0) {
                            throw new exceptions\runtime('Unable to run ' . $this->php . ': ' . $this->php->getStdErr());
                        }
                    }
                } catch (\exception $exception) {
                    $pharBuilt = false;

                    $this->writeErrorInErrorsDirectory($exception->getMessage());
                }

                if ($this->revisionFile !== null && $this->adapter->file_put_contents($this->revisionFile, $revision, \LOCK_EX) === false) {
                    throw new exceptions\runtime('Unable to save last revision in file \'' . $this->revisionFile . '\'');
                }

                $revisions = $this->vcs->getNextRevisions();
            }
        }

        return $pharBuilt;
    }

    public function writeErrorInErrorsDirectory(string $error): static
    {
        if ($this->errorsDirectory !== null) {
            $revision = $this->vcs === null ? null : $this->vcs->getRevision();

            if ($revision === null) {
                throw new exceptions\logic('Revision is undefined');
            }

            $errorFile = $this->errorsDirectory . \DIRECTORY_SEPARATOR . $revision;

            if ($this->adapter->file_put_contents($errorFile, $error, \LOCK_EX | \FILE_APPEND) === false) {
                throw new exceptions\runtime('Unable to save error in file \'' . $errorFile . '\'');
            }
        }

        return $this;
    }

    protected function includeConfigFile(string $path, ?\Closure $callback = null): static
    {
        if ($callback === null) {
            $builder = $this;
            $callback = function ($path) use ($builder) {
                include_once($path);
            };
        }

        return parent::includeConfigFile($path, $callback);
    }

    protected function setArgumentHandlers(): static
    {
        return parent::setArgumentHandlers()
            ->addArgumentHandler(
                function ($script, $argument, $files) {
                    if (count($files) <= 0) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    foreach ($files as $file) {
                        if (file_exists($file) === false) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Runner configuration file path \'%s\' is invalid'), $file));
                        }

                        if (is_readable($file) === false) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Unable to read runner configuration file \'%s\''), $file));
                        }

                        $script->addRunnerConfigurationFile($file);
                    }
                },
                ['-rc', '--runner-configuration-files'],
                '<file>',
                $this->locale->_('Use <file> as configuration file for runner')
            )
            ->addArgumentHandler(
                function ($script, $argument, $path) {
                    if (count($path) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setPhpPath(current($path));
                },
                ['-p', '--php'],
                '<path>',
                $this->locale->_('Path to PHP binary')
            )
            ->addArgumentHandler(
                function ($script, $argument, $directory) {
                    if (count($directory) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setWorkingDirectory(current($directory));
                },
                ['-w', '--working-directory'],
                '<directory>',
                $this->locale->_('Checkout file from repository in <directory>')
            )
            ->addArgumentHandler(
                function ($script, $argument, $directory) {
                    if (count($directory) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setDestinationDirectory(current($directory));
                },
                ['-d', '--destination-directory'],
                '<directory>',
                $this->locale->_('Save phar in <directory>')
            )
            ->addArgumentHandler(
                function ($script, $argument, $directory) {
                    if (count($directory) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setScoreDirectory(current($directory));
                },
                ['-sd', '--score-directory'],
                '<directory>',
                $this->locale->_('Save score in <directory>')
            )
            ->addArgumentHandler(
                function ($script, $argument, $directory) {
                    if (count($directory) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setErrorsDirectory(current($directory));
                },
                ['-ed', '--errors-directory'],
                '<directory>',
                $this->locale->_('Save errors in <directory>')
            )
            ->addArgumentHandler(
                function ($script, $argument, $url) {
                    if (count($url) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->getVcs()->setRepositoryUrl(current($url));
                },
                ['-r', '--repository-url'],
                '<url>',
                $this->locale->_('Url of repository')
            )
            ->addArgumentHandler(
                function ($script, $argument, $file) {
                    if (count($file) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setRevisionFile(current($file));
                },
                ['-rf', '--revision-file'],
                '<file>',
                $this->locale->_('Save last revision in <file>')
            )
            ->addArgumentHandler(
                function ($script, $argument, $version) {
                    if (count($version) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setVersion(current($version));
                },
                ['-v', '--version'],
                '<string>',
                $this->locale->_('Version <string> will be used as version name')
            )
            ->addArgumentHandler(
                function ($script, $argument, $unitTestRunnerScript) {
                    if (count($unitTestRunnerScript) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setUnitTestRunnerScript(current($unitTestRunnerScript));
                },
                ['-utrs', '--unit-test-runner-script']
            )
            ->addArgumentHandler(
                function ($script, $argument, $pharGeneratorScript) {
                    if (count($pharGeneratorScript) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setPharGeneratorScript(current($pharGeneratorScript));
                },
                ['-pgs', '--phar-generator-script']
            )
            ->addArgumentHandler(
                function ($script, $argument, $reportTitle) {
                    if (count($reportTitle) != 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setReportTitle(current($reportTitle));
                },
                ['-rt', '--report-title']
            )
        ;
    }

    final protected function lock(): bool
    {
        $runFile = $this->getRunFile();
        $pid = trim(
            @$this->adapter->file_get_contents($runFile)
        );

        $pid_exists = is_numeric($pid);

        if ($pid_exists !== false && $this->adapter->function_exists('posix_kill')) {
            $pid_exists = $this->adapter->posix_kill($pid, 0);
        }

        if ($pid_exists !== false) {
            throw new exceptions\runtime($this->locale->_('A process has locked run file \'%s\'', $runFile));
        }

        $this->lockResource = @$this->adapter->fopen($runFile, 'w+');

        if ($this->lockResource === false) {
            throw new exceptions\runtime($this->locale->_('Unable to open run file \'%s\'', $runFile));
        }

        if ($this->adapter->flock($this->lockResource, \LOCK_EX | \LOCK_NB) === false) {
            throw new exceptions\runtime($this->locale->_('Unable to get exclusive lock on run file \'%s\'', $runFile));
        }

        $this->adapter->fwrite($this->lockResource, $this->adapter->getmypid());

        return true;
    }

    final protected function unlock(): void
    {
        if ($this->lockResource !== null) {
            $this->adapter->fclose($this->lockResource);

            @$this->adapter->unlink($this->getRunFile());
        }
    }

    protected function doRun(): static
    {
        if ($this->pharCreationEnabled === true && $this->lock()) {
            try {
                $this->createPhar($this->version);
            } catch (\Exception $exception) {
                $this->unlock();

                throw $exception;
            }

            $this->unlock();
        }

        return $this;
    }

    protected function cleanDirectoryPath(string $path): string
    {
        return rtrim($path, DIRECTORY_SEPARATOR);
    }
}
