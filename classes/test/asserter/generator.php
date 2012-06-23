<?php

namespace mageekguy\atoum\test\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter
;

class generator extends asserter\generator
{
	protected $test = null;

	public function __construct(atoum\test $test, atoum\locale $locale = null)
	{
		parent::__construct($locale ?: $test->getLocale());

		$this->setTest($test);
	}

	public function __get($property)
	{
		return $this->test->getAssertionManager()->invoke($property);
	}

	public function __call($method, $arguments)
	{
		return $this->test->getAssertionManager()->invoke($method, $arguments);
	}

	public function setTest(atoum\test $test)
	{
		$this->test = $test;

		return $this;
	}

	public function getTest()
	{
		return $this->test;
	}

	public function asserterPass(atoum\asserter $asserter)
	{
		$this->test->getScore()->addPass();

		return $this;
	}

	public function asserterFail(atoum\asserter $asserter, $reason)
	{
		$file = $this->test->getPath();
		$line = null;
		$class = $this->test->getClass();
		$function = null;
		$method = $this->test->getCurrentMethod();

		foreach (array_filter(debug_backtrace(), function($backtrace) use ($file) { return isset($backtrace['file']) === true && $backtrace['file'] === $file; }) as $backtrace)
		{
			if ($line === null && isset($backtrace['line']) === true)
			{
				$line = $backtrace['line'];
			}

			if ($function === null && isset($backtrace['object']) === true && isset($backtrace['function']) === true && $backtrace['object'] === $asserter && $backtrace['function'] !== '__call')
			{
				$function = $backtrace['function'];
			}
		}

		throw new asserter\exception($reason, $this->test->getScore()->addFail($file, $line, $class, $method, get_class($asserter) . ($function ? '::' . $function : '') . '()', $reason));
	}

	public function getAsserterInstance($asserter, array $arguments = array())
	{
		return parent::getAsserterInstance($asserter, $arguments)->setWithTest($this->test);
	}
}
