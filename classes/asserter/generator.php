<?php

namespace mageekguy\atoum\asserter;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class generator
{
	protected $test = null;
	protected $labels = array();
	protected $aliases = array();
	protected $asserters = array();

	public function __construct(atoum\test $test)
	{
		$this->setTest($test);
	}

	public function __get($asserterName)
	{
		if (isset($this->labels[$asserterName]) === true)
		{
			$asserter = $this->labels[$asserterName];
		}
		else
		{
			$class = $this->getAsserterClass($asserterName);

			if (isset($this->asserters[$class]) === false)
			{
				if (class_exists($class, true) === false)
				{
					throw new exceptions\logic\invalidArgument('Asserter \'' . $class . '\' does not exist');
				}

				$this->asserters[$class] = new $class($this);
			}

			$asserter = $this->asserters[$class];
		}

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

	public function getLabels()
	{
		return $this->labels;
	}

	public function setLabel($label, atoum\asserter $asserter)
	{
		if (isset($this->labels[$label]) === true)
		{
			throw new exceptions\logic\invalidArgument('Label \'' . $label . '\' is already defined');
		}

		$class = $this->getAsserterClass($label);

		if (class_exists($class, true) === true)
		{
			throw new exceptions\logic\invalidArgument('Unable to use \'' . $label . '\' as label because there is an asserter with this name');
		}

		$this->labels[$label] = clone $asserter;

		return $this;
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

	public function setAlias($alias, $asserterClass)
	{
		$this->aliases[$alias] = $asserterClass;

		return $this;
	}

	public function resetAliases()
	{
		$this->aliases = array();

		return $this;
	}
}

?>
