<?php

namespace mageekguy\atoum\report\fields\test;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\report,
	mageekguy\atoum\test\cli,
	mageekguy\atoum\exceptions
;

abstract class event extends report\field
{
	protected $test = null;
	protected $event = null;
	protected $progressBarInjector = null;

	public function __construct(atoum\locale $locale = null)
	{
		parent::__construct(array(
				test::runStart,
				test::fail,
				test::error,
				test::uncompleted,
				test::exception,
				test::success,
				test::runStop
			),
			$locale
		);
	}

	public function getTest()
	{
		return $this->test;
	}

	public function getEvent()
	{
		return $this->event;
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

	public function handleEvent($event, atoum\observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			$this->event = null;

			return false;
		}
		else
		{
			if ($event === test::runStart)
			{
				$this->test = $observable;
			}

			$this->event = $event;

			return true;
		}
	}
}

?>
