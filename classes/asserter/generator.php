<?php

namespace mageekguy\atoum\asserter;

use mageekguy\atoum;
use mageekguy\atoum\exceptions;

class generator
{
	protected $test = null;
	protected $locale = null;
	protected $aliases = array();
	protected $asserters = array();

	public function __construct(atoum\test $test, atoum\locale $locale = null)
	{
		if ($locale === null)
		{
			$locale = new atoum\locale();
		}

		$this
			->setTest($test)
			->setLocale($locale)
		;
	}

	public function __get($asserter)
	{
		$class = $this->getAsserterClass($asserter);

		if (isset($this->asserters[$class]) === false)
		{
			if (class_exists($class, true) === false)
			{
				throw new exceptions\logic\invalidArgument('Asserter \'' . $class . '\' does not exist');
			}

			$this->asserters[$class] = new $class($this->test->getScore(), $this->locale, $this);
		}

		return $this->asserters[$class];
	}

	public function __call($asserter, $arguments)
	{
		$asserter = $this->{$asserter};

		if (sizeof($arguments) > 0)
		{
			call_user_func_array(array($asserter, 'setWith'), $arguments);
		}

		return $asserter;
	}

	public function getTest()
	{
		return $this->test;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function getAliases()
	{
		return $this->aliases;
	}

	public function getAsserterClass($asserter)
	{
		if (isset($this->aliases[$asserter]) === true)
		{
			$asserter = $this->aliases[$asserter];
		}

		if (substr($asserter, 0, 1) != '\\')
		{
			$asserter = __NAMESPACE__ . 's\\' . $asserter;
		}

		return $asserter;
	}

	public function setTest(atoum\test $test)
	{
		$this->test = $test;
		return $this;
	}

	public function setLocale(atoum\locale $locale)
	{
		$this->locale = $locale;
		return $this;
	}

	public function setAlias($alias, $asserter)
	{
		$this->aliases[$alias] = $asserter;

		return $this;
	}

	public function resetAliases()
	{
		$this->aliases = array();

		return $this;
	}
}

?>
