<?php

namespace atoum\atoum\php\tokenizer;

use atoum\atoum\exceptions;
use atoum\atoum\php\tokenizer\iterator\value;

class iterator extends value
{
    protected $key = null;
    protected $size = 0;
    protected $values = [];
    protected $skipedValues = [];

    public function __toString()
    {
        $key = $this->key();

        $string = implode('', iterator_to_array($this));

        if ($key !== null) {
            $this->seek($key);
        }

        return $string;
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return (current($this->values) !== false);
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        $value = null;

        if ($this->valid() === true) {
            $value = current($this->values)->current();
        }

        return $value;
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->key < 0 || $this->key >= $this->size ? null : $this->key;
    }

    public function prev($offset = 1)
    {
        while (($valid = $this->valid()) === true && $offset > 0) {
            $currentValue = current($this->values);

            $currentValue->prev();

            while ($currentValue->valid() === false && $valid === true) {
                prev($this->values);

                if (($valid = $this->valid()) === true) {
                    $currentValue = current($this->values);
                    $currentValue->end();
                }
            }

            if ($valid === true) {
                while (in_array($this->current(), $this->skipedValues) === true && $this->valid() === true) {
                    $this->prev();
                }
            }

            $this->key--;

            $offset--;
        }

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function next($offset = 1)
    {
        while (($valid = $this->valid()) === true && $offset > 0) {
            $currentValue = current($this->values);

            $currentValue->next();

            while ($currentValue->valid() === false && $valid === true) {
                next($this->values);

                if (($valid = $this->valid()) === true) {
                    $currentValue = current($this->values);
                    $currentValue->rewind();
                }
            }

            if ($valid === true) {
                while (in_array($this->current(), $this->skipedValues) === true && $this->valid() === true) {
                    $this->next();
                }
            }

            $this->key++;

            $offset--;
        }

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        if ($this->size > 0) {
            reset($this->values);

            $currentValue = current($this->values);

            $valid = true;

            while ($currentValue->rewind()->valid() == false && $valid === true) {
                next($this->values);

                if (($valid = $this->valid()) === true) {
                    $currentValue = current($this->values);
                }
            }

            $this->key = 0;

            if ($valid === true) {
                while (in_array($this->current(), $this->skipedValues) === true && $this->valid() === true) {
                    $this->next();
                }
            }
        }

        return $this;
    }

    public function end()
    {
        if ($this->size > 0) {
            end($this->values);

            $currentValue = current($this->values);

            $valid = true;

            while ($currentValue->end()->valid() == false && $valid === true) {
                prev($this->values);

                if (($valid = $this->valid()) === true) {
                    $currentValue = current($this->values);
                }
            }

            $this->key = $this->size - 1;

            if ($valid === true) {
                while (in_array($this->current(), $this->skipedValues) === true && $this->valid() === true) {
                    $this->prev();
                }
            }
        }

        return $this;
    }

    public function append(value $value)
    {
        if ($value->parent !== null) {
            throw new exceptions\runtime('Unable to append value because it has already a parent');
        }

        $value->parent = $this;

        $this->values[] = $value;

        if ($this->key === null) {
            $this->key = 0;
        }

        $size = count($value);

        if ($size > 0) {
            $value->rewind();

            $this->size += $size;

            $parent = $this->parent;

            while ($parent !== null) {
                $parent->size += $size;

                $parent = $parent->parent;
            }
        }


        return $this;
    }

    #[\ReturnTypeWillChange]
    public function count()
    {
        return $this->size;
    }

    public function skipValue($value)
    {
        if (in_array($value, $this->skipedValues) === false) {
            $this->skipedValues[] = $value;
        }

        return $this;
    }

    public function getSkipedValues()
    {
        return $this->skipedValues;
    }

    public function reset()
    {
        $this->key = null;
        $this->size = 0;
        $this->values = [];
        $this->parent = null;
        $this->skipedValues = [];

        return $this;
    }

    public function getValue()
    {
        return (current($this->values) ?: null);
    }

    public function seek($key)
    {
        if ($key > count($this) / 2) {
            $this->end();
        } elseif ($this->valid() === false) {
            $this->rewind();
        }

        if ($key > $this->key) {
            $this->next($key - $this->key);
        } else {
            $this->prev($this->key - $key);
        }

        return $this;
    }

    public function findTag($tag)
    {
        foreach ($this as $key => $token) {
            if ($token->getTag() === $tag) {
                return $key;
            }
        }

        return null;
    }

    public function goToNextTagWhichIsNot(array $tags)
    {
        $this->next();

        $token = $this->current();

        while ($token !== null && in_array($token->getTag(), $tags) === true) {
            $this->next();

            $token = $this->current();
        }

        return $this;
    }
}
