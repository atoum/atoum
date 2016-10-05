<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use mageekguy\atoum\php\tokenizer;
use mageekguy\atoum\php\tokenizer\iterators;

class phpClass extends tokenizer\iterator
{
    protected $methods = [];
    protected $constants = [];
    protected $properties = [];

    public function reset()
    {
        $this->methods = [];
        $this->constants = [];
        $this->properties = [];

        return parent::reset();
    }

    public function getConstant($index)
    {
        return (isset($this->constants[$index]) === false ? null : $this->constants[$index]);
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function appendConstant(iterators\phpConstant $phpConstant)
    {
        $this->constants[] = $phpConstant;

        return $this->append($phpConstant);
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getMethod($index)
    {
        return (isset($this->methods[$index]) === false ? null : $this->methods[$index]);
    }

    public function appendMethod(iterators\phpMethod $phpMethod)
    {
        $this->methods[] = $phpMethod;

        return $this->append($phpMethod);
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function getProperty($index)
    {
        return (isset($this->properties[$index]) === false ? null : $this->properties[$index]);
    }

    public function appendProperty(iterators\phpProperty $phpProperty)
    {
        $this->properties[] = $phpProperty;

        return $this->append($phpProperty);
    }
}
