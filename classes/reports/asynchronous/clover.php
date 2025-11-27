<?php

namespace atoum\atoum\reports\asynchronous;

use atoum\atoum;
use atoum\atoum\exceptions;
use atoum\atoum\score;

class clover extends atoum\reports\asynchronous
{
    public const defaultTitle = 'atoum code coverage';
    public const defaultPackage = 'atoumCodeCoverage';
    public const lineTypeMethod = 'method';
    public const lineTypeStatement = 'stmt';
    public const lineTypeConditional = 'cond';

    protected mixed $score = null;
    protected int $loc = 0;
    protected int $coveredLoc = 0;
    protected int $methods = 0;
    protected int $coveredMethods = 0;
    protected int $branches = 0;
    protected int $coveredBranches = 0;
    protected int $paths = 0;
    protected int $classes = 0;
    protected string $package = '';

    public function __construct(?atoum\adapter $adapter = null)
    {
        parent::__construct();

        $this->setAdapter($adapter);

        if ($this->adapter->extension_loaded('libxml') === false) {
            throw new exceptions\runtime('libxml PHP extension is mandatory for clover report');
        }

        if ($this->adapter->extension_loaded('dom') === false) {
            throw new exceptions\runtime('dom PHP extension is mandatory for clover report');
        }
    }

    public function getTitle(): string
    {
        return ($this->title ?: self::defaultTitle);
    }

    public function getPackage(): string
    {
        return ($this->package ?: self::defaultPackage);
    }

    public function setPackage(string $package): static
    {
        $this->package = (string) $package;

        return $this;
    }

    public function handleEvent(string $event, atoum\observable $observable)
    {
        $this->score = ($event !== atoum\runner::runStop ? null : $observable->getScore());

        parent::handleEvent($event, $observable);

        return $this;
    }

    public function build(string $event): static
    {
        if ($event === atoum\runner::runStop) {
            $document = new \DOMDocument('1.0', 'UTF-8');

            $document->formatOutput = true;
            $document->appendChild($this->makeRootElement($document, $this->score->getCoverage()));

            $this->string = $document->saveXML();
        }

        return $this;
    }

    protected function makeRootElement(\DOMDocument $document, score\coverage $coverage): \DOMElement
    {
        $root = $document->createElement('coverage');

        $root->setAttribute('generated', $this->getAdapter()->time());
        $root->setAttribute('clover', $this->getAdapter()->uniqid());

        $root->appendChild($this->makeProjectElement($document, $coverage));

        return $root;
    }

    protected function makeProjectElement(\DOMDocument $document, score\coverage $coverage): \DOMElement
    {
        $project = $document->createElement('project');

        $project->setAttribute('timestamp', $this->getAdapter()->time());
        $project->setAttribute('name', $this->getTitle());

        $project->appendChild($this->makePackageElement($document, $coverage));
        $project->appendChild($this->makeProjectMetricsElement($document, count($coverage->getClasses())));

        return $project;
    }

    protected function makeProjectMetricsElement(\DOMDocument $document, int $files): \DOMElement
    {
        $metrics = $this->makePackageMetricsElement($document, $files);

        $metrics->setAttribute('packages', 1);

        return $metrics;
    }

    protected function makePackageElement(\DOMDocument $document, score\coverage $coverage): \DOMElement
    {
        $package = $document->createElement('package');

        $package->setAttribute('name', $this->getPackage());

        foreach ($coverage->getClasses() as $class => $file) {
            $package->appendChild($this->makeFileElement($document, $file, $class, $coverage->getCoverageForClass($class), $coverage->getBranchesCoverageForClass($class), $coverage->getPathsCoverageForClass($class)));
        }

        $package->appendChild($this->makePackageMetricsElement($document, count($coverage->getClasses())));

        return $package;
    }

    protected function makePackageMetricsElement(\DOMDocument $document, int $files): \DOMElement
    {
        $metrics = $this->makeFileMetricsElement($document, $this->loc, $this->coveredLoc, $this->methods, $this->coveredMethods, $this->classes, $this->branches, $this->coveredBranches, $this->paths);

        $metrics->setAttribute('files', $files);

        return $metrics;
    }

    protected function makeFileElement(\DOMDocument $document, string $filename, string $class, array $coverage, array $branches, array $paths): \DOMElement
    {
        $file = $document->createElement('file');

        $file->setAttribute('name', basename($filename));
        $file->setAttribute('path', $filename);

        $methods = count($coverage);
        $coveredMethods = 0;
        $totalLines = $coveredLines = 0;
        $totalBranches = $coveredBranches = 0;
        $totalPaths = 0;

        foreach ($coverage as $method => $lines) {
            $totalMethodLines = $coveredMethodLines = 0;

            if (isset($branches[$method])) {
                $totalBranches += count($branches[$method]);
                $coveredBranches += count(array_filter($branches[$method], function (array $branch) {
                    return $branch['hit'] === 1;
                }));
            }

            if (isset($paths[$method])) {
                $totalPaths += count($paths[$method]);
            }

            foreach ($lines as $lineNumber => $cover) {
                if ($cover >= -1) {
                    $totalMethodLines++;
                }

                if ($cover === 1) {
                    $coveredMethodLines++;
                    $file->appendChild($this->makeLineElement($document, $lineNumber));
                } else {
                    if ($cover !== -2) {
                        $file->appendChild($this->makeLineElement($document, $lineNumber, 0));
                    }
                }
            }

            if ($coveredMethodLines === $totalMethodLines) {
                ++$coveredMethods;
            }

            $totalLines += $totalMethodLines;
            $coveredLines += $coveredMethodLines;
        }

        $this
            ->addLoc($totalLines)
            ->addCoveredLoc($coveredLines)
            ->addClasses(1)
            ->addMethod($methods)
            ->addCoveredMethod($coveredMethods)
            ->addBranches($totalBranches)
            ->addCoveredBranches($coveredBranches)
            ->addPaths($totalPaths)
        ;

        $file->appendChild($this->makeClassElement($document, $class, $coverage, $branches, $paths));
        $file->appendChild($this->makeFileMetricsElement($document, $totalLines, $coveredLines, $methods, $coveredMethods, 1, $totalBranches, $coveredBranches, $totalPaths));

        return $file;
    }

    protected function makeFileMetricsElement(\DOMDocument $document, int $loc, int $cloc, int $methods, int $coveredMethods, int $classes, int $branches = 0, int $coveredBranches = 0, int $complexity = 0): \DOMElement
    {
        $metrics = $this->makeClassMetricsElement($document, $loc, $cloc, $methods, $coveredMethods, $branches, $coveredBranches, $complexity);

        $metrics->setAttribute('classes', $classes);
        $metrics->setAttribute('loc', $loc);
        $metrics->setAttribute('ncloc', $loc);

        return $metrics;
    }

    protected function makeClassElement(\DOMDocument $document, string $classname, array $coverage, array $branches, array $paths): \DOMElement
    {
        $class = $document->createElement('class');

        $class->setAttribute('name', basename(str_replace('\\', DIRECTORY_SEPARATOR, $classname)));

        $methods = count($coverage);
        $coveredMethods = 0;
        $totalLines = $coveredLines = 0;
        $totalBranches = $coveredBranches = 0;
        $totalPaths = 0;

        foreach ($coverage as $method => $lines) {
            if (isset($branches[$method])) {
                $totalBranches += count($branches[$method]);
                $coveredBranches += count(array_filter($branches[$method], function (array $branch) {
                    return $branch['hit'] === 1;
                }));
            }

            if (isset($paths[$method])) {
                $totalPaths += count($paths[$method]);
            }

            foreach ($lines as $cover) {
                if ($cover >= -1) {
                    $totalLines++;
                }

                if ($cover === 1) {
                    $coveredLines++;
                }
            }

            if ($totalLines === $coveredLines) {
                ++$coveredMethods;
            }
        }

        $class->appendChild($this->makeClassMetricsElement($document, $totalLines, $coveredLines, $methods, $coveredMethods, $totalBranches, $coveredBranches, $totalPaths));

        return $class;
    }

    protected function makeClassMetricsElement(\DOMDocument $document, int $loc, int $coveredLines, int $methods, int $coveredMethods, int $branches = 0, int $coveredBranches = 0, int $complexity = 0): \DOMElement
    {
        $metrics = $document->createElement('metrics');

        $metrics->setAttribute('complexity', $complexity);
        $metrics->setAttribute('elements', $loc + $methods + $branches);
        $metrics->setAttribute('coveredelements', $coveredLines + $coveredMethods + $coveredBranches);
        $metrics->setAttribute('conditionals', $branches);
        $metrics->setAttribute('coveredconditionals', $coveredBranches);
        $metrics->setAttribute('statements', $loc);
        $metrics->setAttribute('coveredstatements', $coveredLines);
        $metrics->setAttribute('methods', $methods);
        $metrics->setAttribute('coveredmethods', $coveredMethods);
        $metrics->setAttribute('testduration', 0);
        $metrics->setAttribute('testfailures', 0);
        $metrics->setAttribute('testpasses', 0);
        $metrics->setAttribute('testruns', 0);

        return $metrics;
    }

    protected function makeLineElement(\DOMDocument $document, int $linenum, int $count = 1): \DOMElement
    {
        $line = $document->createElement('line');

        $line->setAttribute('num', $linenum);
        $line->setAttribute('type', self::lineTypeStatement);
        $line->setAttribute('complexity', 0);
        $line->setAttribute('count', $count);
        $line->setAttribute('falsecount', 0);
        $line->setAttribute('truecount', 0);
        $line->setAttribute('signature', '');
        $line->setAttribute('testduration', 0);
        $line->setAttribute('testsuccess', 0);

        return $line;
    }

    protected function addLoc(int $count): static
    {
        $this->loc += $count;

        return $this;
    }

    protected function addCoveredLoc(int $count): static
    {
        $this->coveredLoc += $count;

        return $this;
    }

    protected function addMethod(int $count): static
    {
        $this->methods += $count;

        return $this;
    }

    protected function addCoveredMethod(int $count): static
    {
        $this->coveredMethods += $count;

        return $this;
    }

    protected function addBranches(int $count): static
    {
        $this->branches += $count;

        return $this;
    }

    protected function addCoveredBranches(int $count): static
    {
        $this->coveredBranches += $count;

        return $this;
    }

    protected function addPaths(int $count): static
    {
        $this->paths += $count;

        return $this;
    }

    protected function addClasses(int $count): static
    {
        $this->classes += $count;

        return $this;
    }
}
