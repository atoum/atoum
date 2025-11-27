<?php

namespace atoum\atoum\iterators\recursives\directory;

use atoum\atoum\iterators\filters;

class factory
{
    protected ?\Closure $dotFilterFactory = null;
    protected ?\Closure $iteratorFactory = null;
    protected bool $acceptDots = false;
    protected ?\Closure $extensionFilterFactory = null;
    protected array $acceptedExtensions = ['php'];

    public function __construct(?\Closure $iteratorFactory = null, ?\Closure $dotFilterFactory = null, ?\Closure $extensionFilterFactory = null)
    {
        $this
            ->setIteratorFactory($iteratorFactory)
            ->setDotFilterFactory($dotFilterFactory)
            ->setExtensionFilterFactory($extensionFilterFactory)
        ;
    }

    public function setIteratorFactory(?\Closure $factory = null): static
    {
        $this->iteratorFactory = $factory ?: function ($path) {
            return new \recursiveDirectoryIterator($path);
        };

        return $this;
    }

    public function getIteratorFactory(): \Closure
    {
        return $this->iteratorFactory;
    }

    public function setDotFilterFactory(?\Closure $factory = null): static
    {
        $this->dotFilterFactory = $factory ?: function ($iterator) {
            return new filters\recursives\dot($iterator);
        };

        return $this;
    }

    public function getDotFilterFactory(): \Closure
    {
        return $this->dotFilterFactory;
    }

    public function setExtensionFilterFactory(?\Closure $factory = null): static
    {
        $this->extensionFilterFactory = $factory ?: function ($iterator, $extensions) {
            return new filters\recursives\extension($iterator, $extensions);
        };

        return $this;
    }

    public function getExtensionFilterFactory(): \Closure
    {
        return $this->extensionFilterFactory;
    }

    public function getIterator(string $path): \Iterator
    {
        $iterator = call_user_func($this->iteratorFactory, $path);

        if ($this->acceptDots === false) {
            $iterator = call_user_func($this->dotFilterFactory, $iterator);
        }

        if (count($this->acceptedExtensions) > 0) {
            $iterator = call_user_func($this->extensionFilterFactory, $iterator, $this->acceptedExtensions);
        }

        return $iterator;
    }

    public function dotsAreAccepted(): bool
    {
        return $this->acceptDots;
    }

    public function acceptDots(): static
    {
        $this->acceptDots = true;

        return $this;
    }

    public function refuseDots(): static
    {
        $this->acceptDots = false;

        return $this;
    }

    public function getAcceptedExtensions(): array
    {
        return $this->acceptedExtensions;
    }

    public function acceptExtensions(array $extensions): static
    {
        $this->acceptedExtensions = [];

        foreach ($extensions as $extension) {
            $this->acceptedExtensions[] = self::cleanExtension($extension);
        }

        return $this;
    }

    public function acceptAllExtensions(): static
    {
        return $this->acceptExtensions([]);
    }

    public function refuseExtension(string $extension): static
    {
        $key = array_search(self::cleanExtension($extension), $this->acceptedExtensions);

        if ($key !== false) {
            unset($this->acceptedExtensions[$key]);

            $this->acceptedExtensions = array_values($this->acceptedExtensions);
        }

        return $this;
    }

    protected static function cleanExtension(string $extension): string
    {
        return trim($extension, '.');
    }
}
