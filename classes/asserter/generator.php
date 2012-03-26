<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class generator
{
	protected $test = null;
	protected $locale = null;
	protected $aliases = array();

	public function __construct(atoum\test $test = null, atoum\locale $locale = null)
	{
		if ($test !== null)
		{
			$this->setTest($test);

			if ($locale === null)
			{
				$locale = $test->getLocale();
			}
		}

		$this->setLocale($locale ?: new atoum\locale());
	}

	public function __get($property)
	{
		return ($this->test === null ? $this->getAsserterInstance($property) : $this->test->getInterpreter()->invoke($property));
	}

	public function __set($asserter, $class)
	{
		$this->setAlias($asserter, $class);
	}

	public function __call($method, $arguments)
	{
		return ($this->test === null ? $this->getAsserterInstance($method, $arguments) : $this->test->getInterpreter()->invoke($method, $arguments));
	}

	public function setTest(atoum\test $test)
	{
		$this->test = $test;

		return $this->setLocale($test->getLocale());
	}

	public function getTest()
	{
		return $this->test;
	}

	public function setLocale(atoum\locale $locale)
	{
		$this->locale = $locale;

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

	public function getScore()
	{
		return $this->test === null ? null : $this->test->getScore();
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

			if (sizeof($arguments) > 0)
			{
				call_user_func_array(array($asserterInstance, 'setWith'), $arguments);
			}

			return $asserterInstance;
		}
	}
}

?>
