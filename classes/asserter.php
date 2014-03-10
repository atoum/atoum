<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\variable
;

abstract class asserter
{
	protected $locale = null;
	protected $analyzer = null;
	protected $generator = null;
	protected $test = null;

	public function __construct(asserter\generator $generator = null, variable\analyzer $analyzer = null)
	{
		$this
			->setLocale()
			->setGenerator($generator)
			->setAnalyzer($analyzer)
		;
	}

	public function __get($asserter)
	{
		return $this->generator->{$asserter};
	}

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'foreach':
				if (isset($arguments[0]) === false || (is_array($arguments[0]) === false && $arguments[0] instanceof \traversable === false))
				{
					throw new exceptions\logic\invalidArgument('First argument of ' . get_class($this) . '::' . $method . '() must be an array or a \traversable instance');
				}
				else if (isset($arguments[1]) === false || $arguments[1] instanceof \closure === false)
				{
					throw new exceptions\logic\invalidArgument('Second argument of ' . get_class($this) . '::' . $method . '() must be a closure');
				}

				foreach ($arguments[0] as $key => $value)
				{
					call_user_func_array($arguments[1], array($this, $value, $key));
				}

				return $this;

			default:
				return $this->generator->__call($method, $arguments);
		}
	}

	public function reset()
	{
		return $this;
	}

	public function setLocale(locale $locale = null)
	{
		$this->locale = $locale ?: new locale();

		if ($this->generator !== null)
		{
			$this->generator->setLocale($this->locale);
		}

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setGenerator(asserter\generator $generator = null)
	{
		$this->generator = $generator ?: new asserter\generator();

		$this->generator->setLocale($this->locale);

		return $this;
	}

	public function getGenerator()
	{
		return $this->generator;
	}

	public function setAnalyzer(variable\analyzer $analyzer = null)
	{
		$this->analyzer = $analyzer ?: new variable\analyzer();

		return $this;
	}

	public function getAnalyzer()
	{
		return $this->analyzer;
	}

	public function setWithTest(test $test)
	{
		$this->test = $test;

		return $this;
	}

	public function setWithArguments(array $arguments)
	{
		if (sizeof($arguments) > 0)
		{
			call_user_func_array(array($this, 'setWith'), $arguments);
		}

		return $this;
	}

	public function setWith($mixed)
	{
		return $this->reset();
	}

	protected function pass()
	{
		$this->generator->asserterPass($this);

		return $this;
	}

	protected function fail($reason)
	{
		$this->generator->asserterFail($this, $reason);

		return $this;
	}

	protected function getTypeOf($mixed)
	{
		return $this->analyzer->getTypeOf($mixed);
	}

	protected function _($string)
	{
		return call_user_func_array(array($this->locale, '_'), func_get_args());
	}

	protected function __($singular, $plural, $quantity)
	{
		return call_user_func_array(array($this->locale, '__'), func_get_args());
	}
}
