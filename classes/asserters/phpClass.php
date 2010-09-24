<?php

namespace mageekguy\atoum\asserters;

class phpClass extends \mageekguy\atoum\asserter
{
	protected $class = null;
	protected $reflectionClassInjecter = null;

	public function getReflectionClass($class)
	{
		return ($this->reflectionClassInjecter === null ? new \reflectionClass($class) : $this->reflectionClassInjecter->__invoke($class));
	}

	public function setReflectionClassInjecter(\closure $reflectionClassInjecter)
	{
		$closure = new \reflectionMethod($reflectionClassInjecter, '__invoke');

		if ($closure->getNumberOfParameters() != 1)
		{
			throw new \runtimeException('Reflection class injecter must take one argument');
		}

		$this->reflectionClassInjecter = $reflectionClassInjecter;

		return $this;
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
		$this->class->getParentClass()->getName() == $parent ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not the parent of class %s'), $parent, $this->class->getName()));

		return $this;
	}

	public function hasInterface($interface, $failMessage = null)
	{
		in_array(ltrim($interface, '\\'), $this->class->getInterfaceNames()) === true  ? $this->pass() : $this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('Class %s does not implement interface %s'), $this->class->getName(), $interface));

		return $this;
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

}

?>
