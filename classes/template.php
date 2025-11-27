<?php

namespace atoum\atoum;

class template extends template\data
{
    protected array $children = [];

    public function __set(string $tag, mixed $data): void
    {
        foreach ($this->getByTag($tag) as $child) {
            $child->setData($data);
        }
    }

    public function __get(string $tag): template\iterator
    {
        return $this->getByTag($tag);
    }

    public function __unset(string $tag): void
    {
        foreach ($this->getByTag($tag) as $child) {
            $child->resetData();
        }
    }

    public function __isset(string $tag): bool
    {
        return (count($this->getByTag($tag)) > 0);
    }

    public function getByTag(string $tag): template\iterator
    {
        $iterator = new template\iterator();

        return $iterator->addTag($tag, $this);
    }

    public function getById(string $id, bool $fromRoot = true): ?self
    {
        $root = $fromRoot === false ? $this : $this->getRoot();

        if ($root->getId() === $id) {
            return $root;
        } else {
            foreach ($root->children as $child) {
                $tag = $child->getById($id, false);

                if ($tag !== null) {
                    return $tag;
                }
            }

            return null;
        }
    }

    public function getChild(int $rank): ?template\data
    {
        return (isset($this->children[$rank]) === false ? null : $this->children[$rank]);
    }

    public function getChildren(): array
    {
        return array_values($this->children);
    }

    public function setWith(iterable $mixed): static
    {
        foreach ($mixed as $tag => $value) {
            $this->{$tag} = $value;
        }

        return $this;
    }

    public function resetChildrenData(): static
    {
        foreach ($this->children as $child) {
            $child->resetData();
        }

        return $this;
    }

    public function build(iterable $mixed = []): static
    {
        foreach ($this->setWith($mixed)->children as $child) {
            $this->addData($child->getData());
        }

        return parent::build();
    }

    public function hasChildren(): bool
    {
        return (count($this->children) > 0);
    }

    public function isChild(template\data $child): bool
    {
        return ($child->parent === $this);
    }

    public function addToParent(iterable $mixed = []): static
    {
        $this->setWith($mixed);

        return parent::addToParent();
    }

    public function addChild(template\data $child): static
    {
        if ($this->isChild($child) === false) {
            $id = $child->getId();

            if ($id !== null && $this->idExists($id) === true) {
                throw new exceptions\runtime('Id \'' . $id . '\' is already defined');
            }

            if ($child->parentIsSet() === true) {
                $child->unsetParent();
            }

            $child->rank = count($this->children);
            $this->children[$child->rank] = $child;
            $child->parent = $this;
        }

        return $this;
    }

    public function deleteChild(template\data $child): static
    {
        if ($this->isChild($child) === true) {
            unset($this->children[$child->rank]);
            $child->parent = null;
            $child->rank = null;
        }

        return $this;
    }

    public function idExists(string $id): bool
    {
        return ($this->getById($id) !== null);
    }

    public function setAttribute(string $name, mixed $value): static
    {
        return $this;
    }

    public function unsetAttribute(string $name): static
    {
        return $this;
    }
}
