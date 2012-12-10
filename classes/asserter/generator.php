<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class generator
{
	protected $locale = null;
	protected $aliases = array();

	public function __construct(atoum\locale $locale = null)
	{
		$this->setLocale($locale);
	}

	public function __set($asserter, $class)
	{
		$this->setAlias($asserter, $class);
	}

	public function __get($property)
	{
		return $this->getAsserterInstance($property);
	}

	public function __call($method, $arguments)
	{
		return $this->getAsserterInstance($method, $arguments);
	}

	public function setLocale(atoum\locale $locale = null)
	{
		$this->locale = $locale ?: new atoum\locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setAlias($alias, $asserterClass)
	{
		$this->aliases[$alias] = $asserterClass;

		return $this;
	}

	public function getAliases()
	{
		return $this->aliases;
	}

	public function resetAliases()
	{
		$this->aliases = array();

		return $this;
	}

	public function asserterPass(atoum\asserter $asserter)
	{
		return $this;
	}

	public function asserterFail(atoum\asserter $asserter, $reason)
	{
            
                if(is_object($reason) && method_exists($reason, '__toString'))
                {
                    $reason = (string) $reason;
                }
                
                if(!is_string($reason)) {
                    throw new exception('Assertion custom message should be either a string or an object implementing __toString()');
                }
                    
            
		throw new exception($reason);
	}

	public function getAsserterClass($asserter)
	{
		$class = (isset($this->aliases[$asserter]) === false ? $asserter : $this->aliases[$asserter]);

		if (substr($class, 0, 1) != '\\')
		{
			$class = __NAMESPACE__ . 's\\' . $class;
		}

		if (class_exists($class, true) === false)
		{
			$class = null;
		}

		return $class;
	}

	public function getAsserterInstance($asserter, array $arguments = array())
	{
		if (($asserterClass = $this->getAsserterClass($asserter)) === null)
		{
			throw new exceptions\logic\invalidArgument('Asserter \'' . $asserter . '\' does not exist');
		}
		else
		{
			$asserterInstance = new $asserterClass($this);

			return $asserterInstance->setWithArguments($arguments);
		}
	}
}
