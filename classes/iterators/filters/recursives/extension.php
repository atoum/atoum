<?php

namespace atoum\atoum\iterators\filters\recursives;

class extension extends \recursiveFilterIterator
{
    protected array $acceptedExtensions = [];

    public function __construct(mixed $mixed, array $acceptedExtensions = [], ?\Closure $iteratorFactory = null)
    {
        if ($mixed instanceof \recursiveIterator) {
            parent::__construct($mixed);
        } else {
            parent::__construct(call_user_func($iteratorFactory ?: function ($path) {
                return new \recursiveDirectoryIterator($path);
            }, (string) $mixed));
        }

        $this->setAcceptedExtensions($acceptedExtensions);
    }

    public function setAcceptedExtensions(array $extensions): static
    {
        array_walk($extensions, function (& $extension) {
            $extension = trim($extension, '.');
        });

        $this->acceptedExtensions = $extensions;

        return $this;
    }

    public function getAcceptedExtensions(): array
    {
        return $this->acceptedExtensions;
    }

    #[\ReturnTypeWillChange]
    public function accept(): bool
    {
        $path = basename((string) $this->getInnerIterator()->current());

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return ($extension == '' || in_array($extension, $this->acceptedExtensions) === true);
    }

    #[\ReturnTypeWillChange]
    public function getChildren(): self
    {
        return new self($this->getInnerIterator()->getChildren(), $this->acceptedExtensions);
    }
}
