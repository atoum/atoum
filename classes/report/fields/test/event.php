<?php

namespace mageekguy\atoum\report\fields\test;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\test\cli,
	mageekguy\atoum\exceptions
;

abstract class event extends report\fields\test
{
	protected $test = null;
	protected $value = null;
	protected $progressBarInjector = null;

	public function getTest()
	{
		return $this->test;
	}

	public function getValue()
	{
		return $this->value;
	}

	public function getProgressBar()
	{
		if ($this->test === null)
		{
			throw new exceptions\logic('Unable to get progress bar because test is undefined');
		}

		return ($this->progressBarInjector === null ? new cli\progressBar($this->test) : $this->progressBarInjector->__invoke($this->test));
	}

	public function setProgressBarInjector(\closure $closure)
	{
		$reflectedClosure = new \reflectionMethod($closure, '__invoke');

		if ($reflectedClosure->getNumberOfParameters() != 1)
		{
			throw new exceptions\logic\invalidArgument('Progress bar injector must take one argument');
		}

		$this->progressBarInjector = $closure;

		return $this;
	}

	public function setWithTest(atoum\test $test, $event = null)
	{
		$this->test = $test;
		$this->value = $event;

		return $this;
	}
}

?>
