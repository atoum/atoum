<?php

namespace mageekguy\atoum\report\fields\test;

use \mageekguy\atoum;
use \mageekguy\atoum\cli;
use \mageekguy\atoum\report;

class event extends report\fields\test
{
	protected $test = null;
	protected $value = null;

	public function getTest()
	{
		return $this->test;
	}

	public function getValue()
	{
		return $this->value;
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
			$progressBar = new cli\progressBar($this->test);
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
