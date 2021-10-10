<?php

namespace atoum\atoum\template;

use atoum\atoum;

class iterator implements \iterator, \countable
{
    protected $tags = [];

    public function addTag($tag, atoum\template $template)
    {
        $children = $template->getChildren();

        while (($child = array_shift($children)) !== null) {
            if ($child->getTag() === $tag) {
                $this->tags[] = $child;
            }

            $children = array_merge($child->getChildren(), $children);
        }

        return $this;
    }

    public function __get($tag)
    {
        $iterator = new self();

        foreach ($this->tags as $innerTag) {
            $iterator->addTag($tag, $innerTag);
        }

        return $iterator;
    }

    public function __set($tag, $data)
    {
        foreach ($this->tags as $innerTag) {
            $innerTag->{$tag} = $data;
        }

        return $this;
    }

    public function __unset($tag)
    {
        foreach ($this->tags as $innerTag) {
            $innerTag->{$tag}->resetData();
        }

        return $this;
    }

    public function __call($method, $arguments)
    {
        foreach ($this->tags as $innerTag) {
            call_user_func_array([$innerTag, $method], $arguments);
        }

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        reset($this->tags);

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return (key($this->tags) !== null);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->tags) ?: null;
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->tags);
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        next($this->tags);

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->tags);
    }
}
