<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\variable
;

abstract class asserter implements asserter\definition
{
	protected $locale = null;
	protected $analyzer = null;
	protected $generator = null;
	protected $test = null;

	public function __construct(asserter\generator $generator = null, variable\analyzer $analyzer = null, locale $locale = null)
	{
		$this
			->setGenerator($generator)
			->setAnalyzer($analyzer)
			->setLocale($locale)
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

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setGenerator(asserter\generator $generator = null)
	{
		$this->generator = $generator ?: new asserter\generator();

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

	public function getTest()
	{
		return $this->test;
	}

	public function setWithTest(test $test)
	{
		$this->test = $test;

		return $this;
	}

	public function setWith($mixed)
	{
		return $this->reset();
	}

	public function setWithArguments(array $arguments)
	{
		if (sizeof($arguments) > 0)
		{
			call_user_func_array(array($this, 'setWith'), $arguments);
		}

		return $this;
	}

	protected function pass()
	{
		if ($this->test !== null)
		{
			$this->test->getScore()->addPass();
		}

		return $this;
	}

	protected function fail($reason)
	{
		if (is_string($reason) === false)
		{
			throw new exceptions\logic\invalidArgument('Fail message must be a string');
		}

		throw new asserter\exception($this, $reason);
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
