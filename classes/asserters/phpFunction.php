<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\exceptions
;

class phpFunction extends atoum\asserter
{
	protected $namespace = '';
	protected $function = '';

	public function setWith($function)
	{
		$this->function = $function;

		return $this;
	}

	public function setWithTest(atoum\test $test)
	{
		$this->namespace = $test->getTestedClassNamespace() . '\\';

		return parent::setWithTest($test);
	}

	public function wasCalled($failMessage = null)
	{
		$fqdn = $this->functionIsSet()->namespace . $this->function;

		if (sizeof($this->getCalls($fqdn)) > 0)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('function %s is called 0 time'), $this->function) . $this->getCallsAsString());
		}

		return $this;
	}

	protected function functionIsSet()
	{
		if ($this->function === null)
		{
			throw new exceptions\logic('Function is undefined');
		}

		return $this;
	}

	protected function getCalls()
	{
		return php\mocker::getAdapter()->getCalls($this->namespace . $this->function);
	}

	protected function getCallsAsString()
	{
		$string = '';

		if (($calls  = $this->getCalls()) !== null)
		{
			$format = '[%' . strlen((string) sizeof($calls)) . 's] %s';

			$phpCalls = array();

			foreach (array_values($calls) as $call => $arguments)
			{
				$phpCalls[] = sprintf($format, $call + 1, new php\call($this->call->getFunction(), $arguments));
			}

			$string = PHP_EOL . join(PHP_EOL, $phpCalls);
		}

		return $string;
	}
}
