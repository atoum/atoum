<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class phpClass extends atoum\asserter
{
	protected $class = null;
	protected $reflectionClassInjector = null;

	public function __get($property)
	{
		switch (strtolower($property))
		{
			case 'isabstract':
			case 'isfinal':
			case 'hasnoparent':
				return $this->{$property}();

			default:
				return parent::__get($property);
		}
	}

	public function __call($method, $arguments)
	{
		switch (strtolower($method))
		{
			case 'extends':
				return call_user_func_array(array($this, 'isSubClassOf'), $arguments);

			case 'implements':
				return call_user_func_array(array($this, 'hasInterface'), $arguments);

			default:
				return parent::__call($method, $arguments);
		}
	}

	public function __toString()
	{
		return (string) $this->getClass();
	}

	public function getReflectionClass($class)
	{
		if ($this->reflectionClassInjector === null)
		{
			$reflectionClass = new \reflectionClass($class);
		}
		else
		{
			$reflectionClass = $this->reflectionClassInjector->__invoke($class);

			if ($reflectionClass instanceof \reflectionClass === false)
			{
				throw new exceptions\runtime\unexpectedValue('Reflection class injector must return a \reflectionClass instance');
			}
		}

		return $reflectionClass;
	}

	public function setReflectionClassInjector(\closure $reflectionClassInjector)
	{
		$closure = new \reflectionMethod($reflectionClassInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new exceptions\logic\invalidArgument('Reflection class injector must take one argument');
		}

		$this->reflectionClassInjector = $reflectionClassInjector;

		return $this;
	}

	public function getClass()
	{
		return ($this->class === null ? null : $this->class->getName());
	}

	public function setWith($class)
	{
		parent::setWith($class);

		try
		{
			$this->class = $this->getReflectionClass($class);
		}
		catch (\exception $exception)
		{
			$this->fail($this->_('Class \'%s\' does not exist', $class));
		}

		$this->pass();

		return $this;
	}

	public function hasParent($parent, $failMessage = null)
	{
		$parentClass = $this->classIsSet()->class->getParentClass();

		if ($parentClass !== false && strtolower($parentClass->getName()) == strtolower($parent))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not the parent of class %s', $parent, $this));
		}

		return $this;
	}

	public function hasNoParent($failMessage = null)
	{
		if (($parentClass = $this->classIsSet()->class->getParentClass()) === false)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s has parent %s', $this, $parentClass));
		}

		return $this;
	}

	public function isSubClassOf($parent, $failMessage = null)
	{
		try
		{
			if ($this->classIsSet()->class->isSubClassOf($parent) == true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage ?: $this->_('%s does not extend %s', $this, $parent));
			}
		}
		catch (\reflectionException $exception)
		{
			throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be a class name', null, $exception);
		}

		return $this;
	}

	public function hasInterface($interface, $failMessage = null)
	{
		try
		{
			if ($this->classIsSet()->class->implementsInterface($interface) === true)
			{
				$this->pass();
			}
			else
			{
				$this->fail($failMessage ?: $this->_('%s does not implement %s', $this, $interface));
			}
		}
		catch (\reflectionException $exception)
		{
			throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be an interface name', null, $exception);
		}
		
		return $this;
	}

	public function isAbstract($failMessage = null)
	{
		if ($this->classIsSet()->class->isAbstract() === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not abstract', $this));
		}

		return $this;
	}

	public function isFinal($failMessage = null)
	{
		if ($this->classIsSet()->class->isFinal() === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not final', $this));
		}

		return $this;
	}

	public function hasMethod($method, $failMessage = null)
	{
		if ($this->classIsSet()->class->hasMethod($method) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s::%s() does not exist', $this, $method));
		}

		return $this;
	}

	public function hasConstant($constant, $failMessage = null)
	{
		if ($this->classIsSet()->class->hasConstant($constant) === false)
		{
			$this->fail($failMessage ?: $this->_('%s::%s does not exist', $this, $constant));

			return $this;
		}
		else
		{
			$this->pass();

			return $this->generator->constant($this->class->getConstant($constant));
		}

	}

	protected function classIsSet()
	{
		if ($this->class === null)
		{
			throw new exceptions\logic('Class is undefined');
		}

		return $this;
	}
}
