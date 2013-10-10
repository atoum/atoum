<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class phpFunction extends atoum\asserter
{
	protected $namespace = '';
	protected $function = '';

	public function __toString()
	{
		return $this->getFullyQualifiedFunctionName();
	}

	public function setNamespace($namespace)
	{
		$this->namespace = trim($namespace, '\\');

		if ($this->namespace !== '')
		{
			$this->namespace .= '\\';
		}

		return $this;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function setFunction($function)
	{
		$this->function = $function;

		return $this;
	}

	public function getFunction()
	{
		return $this->function;
	}

	public function getFullyQualifiedFunctionName()
	{
		return ($this->function === '' ? '' : $this->namespace . $this->function);
	}

	public function setWith($function)
	{
		return $this->setFunction($function);
	}

	public function setWithTest(atoum\test $test)
	{
		$this->setNamespace($test->getTestedClassNamespace());

		return parent::setWithTest($test);
	}

	public function isCalled($failMessage = null)
	{
		if (sizeof($this->functionIsSet()->getCalls()) > 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('function %s is called 0 time'), $this) . $this->getCallsAsString());
		}

		return $this;
	}

	protected function functionIsSet()
	{
		if ($this->getFullyQualifiedFunctionName() === '')
		{
			throw new exceptions\logic('Function is undefined');
		}

		if (function_exists($this->getFullyQualifiedFunctionName()) === false)
		{
			throw new exceptions\logic('Function \'' . $this->getFullyQualifiedFunctionName() . '\' does not exist');
		}

		return $this;
	}

	protected function getCalls()
	{
		return php\mocker::getAdapter()->getCalls(new test\adapter\call($this->getFullyQualifiedFunctionName()));
	}

	protected function getCallsAsString()
	{
		return (sizeof($calls = $this->getCalls()) <= 0 ? '' : PHP_EOL . rtrim($calls));
	}
}
