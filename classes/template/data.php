<?php

namespace atoum\atoum\template;

use atoum\atoum;

class data
{
    protected ?atoum\template $parent = null;
    protected ?int $rank = null;
    protected ?string $data = null;

    public function __construct(mixed $data = null)
    {
        $this->setData($data);
    }

    public function __toString(): string
    {
        return $this->getData();
    }

    public function resetData(): static
    {
        $this->data = null;

        return $this;
    }

    public function getData(): string
    {
        return ($this->data === null ? '' : $this->data);
    }

    public function setData(mixed $data): static
    {
        return $this->resetData()->addData($data);
    }

    public function addData(mixed $data): static
    {
        $data = (string) $data;

        if ($data != '') {
            $this->data .= $data;
        }

        return $this;
    }

    public function setParent(atoum\template $parent): static
    {
        $parent->addChild($this);

        return $this;
    }

    public function getParent(): ?atoum\template
    {
        return $this->parent;
    }

    public function getRoot(): self|atoum\template
    {
        $root = $this;

        while ($root->parent !== null) {
            $root = $root->parent;
        }

        return $root;
    }

    public function isRoot(): bool
    {
        return ($this->parent === null);
    }

    public function parentIsSet(): bool
    {
        return ($this->parent !== null);
    }

    public function unsetParent(): static
    {
        if ($this->parentIsSet() === true) {
            $this->parent->deleteChild($this);
        }

        return $this;
    }

    public function build(): static
    {
        return $this;
    }

    public function addToParent(): static
    {
        if ($this->build()->parentIsSet() === true) {
            $this->parent->addData($this);
        }

        return $this;
    }

    public function getTag(): ?string
    {
        return null;
    }

    public function getId(): ?string
    {
        return null;
    }

    public function getByTag(string $tag): iterator
    {
        return new iterator();
    }

    public function getById(string $id, bool $fromRoot = true): ?atoum\template
    {
        return null;
    }

    public function hasChildren(): bool
    {
        return false;
    }

    public function getChild(int $rank): ?self
    {
        return null;
    }

    public function getChildren(): array
    {
        return [];
    }
}
