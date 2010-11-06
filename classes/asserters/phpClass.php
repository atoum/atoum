<?php

namespace mageekguy\atoum\asserters;

class phpClass extends \mageekguy\atoum\asserter
{
	protected $class = null;
	protected $reflectionClassInjector = null;

	public function getReflectionClass($class)
	{
		return ($this->reflectionClassInjector === null ? new \reflectionClass($class) : $this->reflectionClassInjector->__invoke($class));
	}

	public function setReflectionClassInjector(\closure $reflectionClassInjector)
	{
		$closure = new \reflectionMethod($reflectionClassInjector, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new \runtimeException('Reflection class injector must take one argument');
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
		try
		{
			$this->class = $this->getReflectionClass($class);
		}
		catch (\exception $exception)
		{
			$this->fail(sprintf($this->locale->_('%s is not a class'), $class));
		}

		$this->pass();

		return $this;
	}

	public function hasParent($parent, $failMessage = null)
	{
		if ($this->classIsSet()->class->getParentClass()->getName() == $parent)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not the parent of class %s'), $parent, $this->class->getName()));
		}
	}

	public function hasInterface($interface, $failMessage = null)
	{
		if (in_array(ltrim($interface, '\\'), $this->classIsSet()->class->getInterfaceNames()) === true)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('Class %s does not implement interface %s'), $this->class->getName(), $interface));
		}
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

	protected function classIsSet()
	{
		if ($this->class === null)
		{
			throw new \logicException('Class is undefined');
		}

		return $this;
	}
}

?>
