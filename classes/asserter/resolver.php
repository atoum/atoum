<?php

namespace atoum\atoum\asserter;

use atoum\atoum\tools\variable\analyzer;

class resolver
{
    public const defaultBaseClass = 'atoum\atoum\asserter';
    public const defaultNamespace = 'atoum\atoum\asserters';

    protected string $baseClass = '';
    protected array $namespaces = [];
    private ?analyzer $analyzer = null;
    private array $resolved = [];

    public function __construct(?string $baseClass = null, ?string $namespace = null, ?analyzer $analyzer = null)
    {
        $this
            ->setBaseClass($baseClass ?: static::defaultBaseClass)
            ->addNamespace($namespace ?: static::defaultNamespace)
            ->setAnalyzer($analyzer)
        ;
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

    public function setBaseClass(string $baseClass): static
    {
        $this->baseClass = trim($baseClass, '\\');

        return $this;
    }

    public function getBaseClass(): string
    {
        return $this->baseClass;
    }

    public function addNamespace(string $namespace): static
    {
        $this->namespaces[] = trim($namespace, '\\');

        return $this;
    }

    public function getNamespaces(): array
    {
        return $this->namespaces;
    }

    public function resolve(string $asserter): ?string
    {
        if (isset($this->resolved[$asserter])) {
            return $this->resolved[$asserter];
        }

        if (false === $this->analyzer->isValidNamespace($asserter)) {
            return null;
        }

        $class = null;

        if (strpos($asserter, '\\') !== false) {
            $class = $this->checkClass($asserter);
        } else {
            foreach ($this->namespaces as $namespace) {
                $class = $this->checkClass($namespace . '\\' . $asserter);

                if ($class !== null) {
                    break;
                }

                $class = $this->checkClass($namespace . '\\php' . ucfirst($asserter));

                if ($class !== null) {
                    break;
                }
            }
        }

        $this->resolved[$asserter] = $class;

        return $class;
    }

    private function checkClass($class)
    {
        return (class_exists($class, true) === false || is_subclass_of($class, $this->baseClass) === false ? null : $class);
    }
}
