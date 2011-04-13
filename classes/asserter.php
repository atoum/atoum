<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

abstract class asserter
{
	protected $generator = null;

	public function __construct(asserter\generator $generator)
	{
		$this->generator = $generator;
	}

	public function __get($asserter)
	{
		return $this->generator->__get($asserter);
	}

	public function __call($asserter, $arguments)
	{
		return $this->generator->__call($asserter, $arguments);
	}

	public function getScore()
	{
		return $this->generator->getScore();
	}

	public function getLocale()
	{
		return $this->generator->getLocale();
	}

	public function getGenerator()
	{
		return $this->generator;
	}

	public function toString($mixed)
	{
		switch (true)
		{
			case is_bool($mixed):
				return sprintf($this->getLocale()->_('boolean(%s)'), ($mixed == false ? $this->getLocale()->_('false') : $this->getLocale()->_('true')));

			case is_integer($mixed):
				return sprintf($this->getLocale()->_('integer(%s)'), $mixed);

			case is_float($mixed):
				return sprintf($this->getLocale()->_('float(%s)'), $mixed);

			case is_null($mixed):
				return 'null';

			case is_object($mixed):
				return sprintf($this->getLocale()->_('object(%s)'), get_class($mixed));

			case is_resource($mixed):
				return sprintf($this->getLocale()->_('resource(%s)'), $mixed);

			case is_string($mixed):
				return sprintf($this->getLocale()->_('string(%s) \'%s\''), strlen($mixed), $mixed);

			case is_array($mixed):
				return sprintf($this->getLocale()->_('array(%s)'), sizeof($mixed));
		}
	}

	public function is($label)
	{
		return $this->setLabel($label);
	}

	public function setLabel($label)
	{
		if ($label !== null)
		{
			$this->generator->setLabel($label, $this);
		}

		return $this;
	}

	public abstract function setWith($mixed, $label = null);

	protected function pass()
	{
		$this->getScore()->addPass();
		return $this;
	}

	protected function fail($reason)
	{
		$test = $this->generator->getTest();

		$class = $test->getClass();
		$method = $test->getCurrentMethod();
		$file = $test->getPath();
		$line = null;
		$function = null;

		foreach (debug_backtrace() as $backtrace)
		{
			if ($line === null && isset($backtrace['file']) === true && $backtrace['file'] === $file && isset($backtrace['line']) === true)
			{
				$line = $backtrace['line'];
			}

			if ($function === null && isset($backtrace['object']) === true && isset($backtrace['function']) === true)
			{
				if (get_class($backtrace['object']) === get_class($this) && $backtrace['function'] != __FUNCTION__)
				{
					$function = $backtrace['function'];
				}
				else if (is_a($backtrace['object'], __NAMESPACE__ . '\asserter\generator') === true && $backtrace['function'] == '__call')
				{
					$function = $backtrace['args'][0];
				}
			}
		}

		throw new asserter\exception($reason, $this->getScore()->addFail($file, $line, $class, $method, get_class($this) . '::' . $function . '()', $reason));
	}
}

?>
