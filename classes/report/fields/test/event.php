<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\cli;
use \mageekguy\atoum\report;

class event extends report\fields\test
{
	protected $test = null;
	protected $value = null;
	protected $progressBarInjecter = null;

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
			throw new \logicException('Unable to get progress bar because test is undefined');
		}

		return ($this->progressBarInjecter === null ? new cli\progressBar($this->test) : $this->progressBarInjecter->__invoke($this->test));
	}

	public function setProgressBarInjecter(\closure $closure)
	{
		$reflectedClosure = new \reflectionMethod($closure, '__invoke');

		if ($reflectedClosure->getNumberOfParameters() != 1)
		{
			throw new \invalidArgumentException('Progress bar injector must take one argument');
		}

		$this->progressBarInjecter = $closure;

		return $this;
	}


	public function setWithTest(atoum\test $test, $event = null)
	{
		$this->test = $test;
		$this->value = $event;

		return $this;
	}

	public function toString()
	{
		static $progressBar = null;

		$string = '';

		if ($this->value === atoum\test::runStart)
		{
			$progressBar = $this->getProgressBar();
			$string = (string) $progressBar;
		}
		else if ($progressBar !== null)
		{
			if ($this->value === atoum\test::runStop)
			{
				$progressBar = null;
				$string = PHP_EOL;
			}
			else
			{
				switch ($this->value)
				{
					case atoum\test::success:
						$progressBar->refresh('S');
						break;

					case atoum\test::fail:
						$progressBar->refresh('F');
						break;

					case atoum\test::error:
						$progressBar->refresh('e');
						break;

					case atoum\test::exception:
						$progressBar->refresh('E');
						break;
				}

				$string = (string) $progressBar;
			}
		}

		return $string;
	}
}

?>
