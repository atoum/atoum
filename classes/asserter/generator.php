<?php

namespace mageekguy\atoum\asserter;

use \mageekguy\atoum;

class generator
{
	protected $score = null;
	protected $locale = null;
	protected $aliases = array();
	protected $asserters = array();

	public function __construct(atoum\score $score, atoum\locale $locale)
	{
		$this
			->setScore($score)
			->setLocale($locale)
		;
	}

	public function __call($asserter, $arguments)
	{
		$class = $this->getAsserterClass($asserter);

		if (isset($this->asserters[$class]) === false)
		{
			if (class_exists($class, true) === false)
			{
				throw new \logicException('Asserter \'' . $class . '\' does not exist');
			}

			$this->asserters[$class] = new $class($this->score, $this->locale, $this);
		}

		if (sizeof($arguments) > 0)
		{
			call_user_func_array(array($this->asserters[$class], 'setWith'), $arguments);
		}

		return $this->asserters[$class];
	}

	public function getScore()
	{
		return $this->score;
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

	public function setScore(atoum\score $score)
	{
		$this->score = $score;
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
