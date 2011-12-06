<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

/**
 * @method  mageekguy\atoum\asserters\adapter               adapter()
 * @method  mageekguy\atoum\asserters\afterDestructionOf    afterDestructionOf()
 * @method  mageekguy\atoum\asserters\boolean               boolean()
 * @method  mageekguy\atoum\asserters\castToString          castToString()
 * @method  mageekguy\atoum\asserters\dateTime              dateTime()
 * @method  mageekguy\atoum\asserters\error                 error()
 * @method  mageekguy\atoum\asserters\exception             exception()
 * @method  mageekguy\atoum\asserters\float                 float()
 * @method  mageekguy\atoum\asserters\hash                  hash()
 * @method  mageekguy\atoum\asserters\integer               integer()
 * @method  mageekguy\atoum\asserters\mock                  mock()
 * @method  mageekguy\atoum\asserters\mysqlDateTime         mysqlDateTime()
 * @method  mageekguy\atoum\asserters\object                object()
 * @method  mageekguy\atoum\asserters\output                output()
 * @method  mageekguy\atoum\asserters\phpArray              phpArray()
 * @method  mageekguy\atoum\asserters\phpClass              phpClass()
 * @method  mageekguy\atoum\asserters\sizeOf                sizeOf()
 * @method  mageekguy\atoum\asserters\stream                stream()
 * @method  mageekguy\atoum\asserters\string                string()
 * @method  mageekguy\atoum\asserters\testedClass           testedClass()
 * @method  mageekguy\atoum\asserters\variable              variable()
 */
class generator
{
    /**
     * @var mageekguy\atoum\test
     */
	protected $test = null;

    /**
     * @var array
     */
	protected $aliases = array();


    /**
     * Constructor
     *
     * @param mageekguy\atoum\test $test
     */
	public function __construct(atoum\test $test)
	{
		$this->setTest($test);
	}


    /**
     * Magic getter
     *
     * @param string $asserterName
     *
     * @return mageekguy\atoum\asserter
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function __get($asserterName)
	{
		switch ($asserterName)
		{
			case 'if':
			case 'then':
			case 'and':
				return $this;

			default:
				$class = $this->getAsserterClass($asserterName);

				if (class_exists($class, true) === false)
				{
					throw new exceptions\logic\invalidArgument('Asserter \'' . $class . '\' does not exist');
				}

				return new $class($this);
		}
	}


    /**
     * Magic setter
     *
     * @param string $asserter
     * @param string $class
     */
	public function __set($asserter, $class)
	{
		$this->setAlias($asserter, $class);
	}


    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mageekguy\atoum\asserter
     */
	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'assert':
				$this->getTest()->stopCase();

				$case = isset($arguments[0]) === false ? null : $arguments[0];

				if ($case !== null)
				{
					$this->getTest()->startCase($case);
				}

				return $this;

			case 'if':
			case 'and':
				return $this;

			default:
				$asserter = $this->{$method};

				if (sizeof($arguments) > 0)
				{
				}
					call_user_func_array(array($asserter, 'setWith'), $arguments);

				return $asserter;
		}
	}


    /**
     * @return mageekguy\atoum\test
     */
	public function getTest()
	{
		return $this->test;
	}


    /**
     * @return mageekguy\atoum\score
     */
	public function getScore()
	{
		return $this->test->getScore();
	}


    /**
     * @return mageekguy\atoum\locale
     */
	public function getLocale()
	{
		return $this->test->getLocale();
	}


    /**
     * @param string $asserter
     *
     * @return string
     */
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


    /**
     * @param mageekguy\atoum\test $test
     *
     * @return mageekguy\atoum\generator
     */
	public function setTest(atoum\test $test)
	{
		$this->test = $test;

		return $this;
	}


    /**
     * @param string $alias
     * @param string $asserterClass
     *
     * @return mageekguy\atoum\generator
     */
	public function setAlias($alias, $asserterClass)
	{
		$this->aliases[$alias] = $asserterClass;

		return $this;
	}


    /**
     * @return array
     */
	public function getAliases()
	{
		return $this->aliases;
	}


    /**
     * @return mageekguy\atoum\generator
     */
	public function resetAliases()
	{
		$this->aliases = array();

		return $this;
	}


    /**
     * @param \closure $closure
     *
     * @return mageekguy\atoum\generator
     */
	public function when(\closure $closure)
	{
		$closure();

		return $this;
	}
}

?>
