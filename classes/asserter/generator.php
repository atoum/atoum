<?php

namespace mageekguy\atoum\asserter;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class generator
{
	protected $test = null;
	protected $aliases = array();
	protected $asserters = array();

	public function __construct(atoum\test $test)
	{
		$this->setTest($test);
	}

	public function __get($asserterName)
	{
		$class = $this->getAsserterClass($asserterName);

		if (class_exists($class, true) === false)
		{
			throw new exceptions\logic\invalidArgument('Asserter \'' . $class . '\' does not exist');
		}

		$this->asserters[] = $asserter = new $class($this);

		return $asserter;
	}

	public function __set($asserter, $class)
	{
		$this->setAlias($asserter, $class);
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

	public function resetAsserters()
	{
		foreach ($this->asserters as $asserter)
		{
			$asserter->reset();
		}

		return $this;
	}

	public function getTest()
	{
		return $this->test;
	}

	public function getScore()
	{
		return $this->test->getScore();
	}

	public function getLocale()
	{
		return $this->test->getLocale();
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
}

?>
