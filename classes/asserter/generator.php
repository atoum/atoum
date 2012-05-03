<?php

namespace mageekguy\atoum\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

/**
 * @property    mageekguy\atoum\asserter                       if
 * @property    mageekguy\atoum\asserter                       and
 * @property    mageekguy\atoum\asserter                       then
 *
 * @method      mageekguy\atoum\asserter                       if()
 * @method      mageekguy\atoum\asserter                       and()
 * @method      mageekguy\atoum\asserter                       then()
 *
 * @method      mageekguy\atoum\asserters\adapter              adapter()
 * @method      mageekguy\atoum\asserters\afterDestructionOf   afterDestructionOf()
 * @method      mageekguy\atoum\asserters\phpArray             array()
 * @method      mageekguy\atoum\asserters\boolean              boolean()
 * @method      mageekguy\atoum\asserters\castToString         castToString()
 * @method      mageekguy\atoum\asserters\phpClass             class()
 * @method      mageekguy\atoum\asserters\dateTime             dateTime()
 * @method      mageekguy\atoum\asserters\error                error()
 * @method      mageekguy\atoum\asserters\exception            exception()
 * @method      mageekguy\atoum\asserters\float                float()
 * @method      mageekguy\atoum\asserters\hash                 hash()
 * @method      mageekguy\atoum\asserters\integer              integer()
 * @method      mageekguy\atoum\asserters\mock                 mock()
 * @method      mageekguy\atoum\asserters\mysqlDateTime        mysqlDateTime()
 * @method      mageekguy\atoum\asserters\object               object()
 * @method      mageekguy\atoum\asserters\output               output()
 * @method      mageekguy\atoum\asserters\phpArray             phpArray()
 * @method      mageekguy\atoum\asserters\phpClass             phpClass()
 * @method      mageekguy\atoum\asserters\sizeOf               sizeOf()
 * @method      mageekguy\atoum\asserters\stream               stream()
 * @method      mageekguy\atoum\asserters\string               string()
 * @method      mageekguy\atoum\asserters\testedClass          testedClass()
 * @method      mageekguy\atoum\asserters\variable             variable()
 */
class generator
{
    /**
     *
     * @var mageekguy\atoum\locale
     */
	protected $locale = null;

    /**
     * @var array
     */
	protected $aliases = array();


    /**
     * Constructor
     *
     * @param mageekguy\atoum\locale $locale
     */
	public function __construct(atoum\locale $locale = null)
	{
		$this->setLocale($locale ?: new atoum\locale());
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
     * Magic getter
     *
     * @param string $asserterName
     *
     * @return mageekguy\atoum\asserter
     *
     * @throws mageekguy\atoum\exceptions\logic\invalidArgument
     */
	public function __get($property)
	{
		return $this->getAsserterInstance($property);
	}


    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mageekguy\atoum\asserter
     */
	public function __call($method, $arguments)
	{
		return $this->getAsserterInstance($method, $arguments);
	}


    /**
     * @param mageekguy\atoum\locale $locale
     *
     * @return mageekguy\atoum\locale
     */
	public function setLocale(atoum\locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}


    /**
     * @return mageekguy\atoum\locale
     */
	public function getLocale()
	{
		return $this->locale;
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


	public function asserterPass(atoum\asserter $asserter)
	{
		return $this;
	}


    public function asserterFail(atoum\asserter $asserter, $reason)
	{
		throw new exception($reason);
	}


    /**
     * @param string $asserter
     *
     * @return string
     */
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

?>
