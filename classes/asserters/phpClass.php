<?php

namespace mageekguy\atoum\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class phpClass extends atoum\asserter
{
	protected $class = null;
	protected $reflectionClassInjector = null;

	public function getReflectionClass($class)
	{
		$reflectionClass = null;

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

	public function setWith($class, $label = null)
	{
		try
		{
			$this->class = $this->getReflectionClass($class);
		}
		catch (\exception $exception)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a class'), $class));
		}

		$this->pass();

		return $this->setLabel($label);
	}

	public function hasParent($parent, $failMessage = null)
	{
		$parentClass = $this->classIsSet()->class->getParentClass();

		if ($parentClass !== false && $parentClass->getName() == $parent)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not the parent of class %s'), $parent, $this->class->getName()));
		}

		return $this;
	}

	public function isSubClassOf($parent, $failMessage = null)
	{
		if ($this->classIsSet()->class->isSubClassOf($parent) == true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not a sub-class of %s'), $this->class->getName(), $parent));
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
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('class %s has parent %s'), $this->class->getName(), $parentClass));
		}

		return $this;
	}

	public function hasInterface($interface, $failMessage = null)
	{
		if (in_array(ltrim($interface, '\\'), $this->classIsSet()->class->getInterfaceNames()) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Class %s does not implement interface %s'), $this->class->getName(), $interface));
		}

		return $this;
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

?>
