<?php

namespace mageekguy\atoum\php\tokenizer\iterator;

use mageekguy\atoum\exceptions;

abstract class value implements \iterator, \countable
{
    protected $parent = null;

    public function setParent(self $parent)
    {
        if ($this->parent !== null) {
            throw new exceptions\runtime('Parent is already set');
        }

        $parent->append($this);

        return $this;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getRoot()
    {
        $root = null;

        $parent = $this->getParent();

        while ($parent !== null) {
            $root = $parent;

            $parent = $parent->getParent();
        }

        return $root;
    }

    abstract public function __toString();
    abstract public function prev();
    abstract public function end();
    abstract public function append(self $value);
    abstract public function getValue();
    abstract public function seek($key);
}
