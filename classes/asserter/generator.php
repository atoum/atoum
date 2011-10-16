<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

/**
 * @method   asserters\boolean      boolean()
 * @method   asserters\error        error()
 * @method   asserters\exception    exception()
 * @method   asserters\float        float()
 * @method   asserters\hash         hash()
 * @method   asserters\integer      integer()
 * @method   asserters\object       object()
 * @method   asserters\string       string()
 * @method   asserters\variable     variable()
 */
class generator
{
    /**
     * @var atoum\test
     */
	protected $test = null;

    /**
     * @var array
     */
	protected $aliases = array();


    /**
     * Constructor
     *
     * @param atoum\test $test
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
     * @return atoum\asserter
     *
     * @throws exceptions\logic\invalidArgument
     */
	public function __get($asserterName)
	{
		$class = $this->getAsserterClass($asserterName);

		if (class_exists($class, true) === false)
		{
			throw new exceptions\logic\invalidArgument('Asserter \'' . $class . '\' does not exist');
		}

		return new $class($this);
	}


    /**
     * Magic setter
     *
     * @param type $asserter
     * @param type $class
     */
	public function __set($asserter, $class)
	{
		$this->setAlias($asserter, $class);
	}


    /**
     * @param string $asserter
     * @param array  $arguments
     *
     * @return atoum\asserter
     */
	public function __call($asserter, $arguments)
	{
		$asserter = $this->{$asserter};

		if (sizeof($arguments) > 0)
		{
			call_user_func_array(array($asserter, 'setWith'), $arguments);
		}

		return $asserter;
	}


    /**
     * @return atoum\test
     */
	public function getTest()
	{
		return $this->test;
	}


    /**
     * @return score
     */
	public function getScore()
	{
		return $this->test->getScore();
	}


    /**
     * @return locale
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
     * @param atoum\test $test
     *
     * @return generator
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
     * @return generator
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
     * @return generator
     */
	public function resetAliases()
	{
		$this->aliases = array();

		return $this;
	}


    /**
     * @param \closure $closure
     *
     * @return generator
     */
	public function when(\closure $closure)
	{
		$closure();

		return $this;
	}
}

?>
