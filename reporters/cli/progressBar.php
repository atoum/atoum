<?php

namespace mageekguy\atoum\reporters\cli;

use \mageekguy\atoum;

class progressBar extends atoum\reporter
{
	protected $numberOfTests = 0;
	protected $currentTestNumber = 0;
	protected $progressBar = '';
	protected $counter = '';
	protected $toString = 0;
	protected $refresh = '';

	public function __construct(atoum\test $test)
	{
		$this->numberOfTests = sizeof($test);
	}

	public function __toString()
	{
		$string = '';

		if ($this->toString == 0)
		{
			$this->progressBar = '[';

			if ($this->numberOfTests > 60)
			{
				$this->progressBar .= str_repeat('.', 59) . '>';
			}
			else
			{
				$this->progressBar .= str_pad(str_repeat('.', $this->numberOfTests), 60, '_', STR_PAD_RIGHT);
			}

			$this->progressBar .= ']';

			$this->counter = '[' . sprintf('%' . strlen((string) $this->numberOfTests) . 'd', $this->currentTestNumber) . '/' . $this->numberOfTests . ']';

			$string .= $this->progressBar . $this->counter;
		}

		if ($this->refresh != '')
		{
			$refreshLength = strlen($this->refresh);

			$this->currentTestNumber += $refreshLength;

			$string .= str_repeat("\010", strlen($this->progressBar) - $refreshLength) . str_repeat("\010", strlen($this->counter));

			$this->progressBar = $this->refresh . substr($this->progressBar, $refreshLength + 1);
			$this->counter = '[' . sprintf('%' . strlen((string) $this->numberOfTests) . 'd', $this->currentTestNumber) . '/' . $this->numberOfTests . ']';

			$string .= $this->progressBar;
			$string .= $this->counter;

			if ($this->numberOfTests > 60 && $this->currentTestNumber % 59 == 0)
			{
				$this->progressBar = '[' . str_pad(str_repeat('.', min(60, $this->numberOfTests - $this->currentTestNumber)), 60, '_', STR_PAD_RIGHT) . ']';
				$this->counter = '';

				$string .= "\n" . $this->progressBar;
			}

			$this->refresh = '';
		}

		$this->toString++;

//			if ($this->refresh != '')
//			{
//				$this->string = '';
//
//				$refreshLength = strlen($this->refresh);
//
//				if ($refreshLength > 0)
//				{
//					$this->currentTestNumber += $refreshLength;
//					$this->progressBar = $this->refresh . substr($this->progressBar, $refreshLength + 1);
//					$this->counter = '[' . sprintf('%' . strlen((string) $this->numberOfTests) . 'd', $this->currentTestNumber) . '/' . $this->numberOfTests . ']';
//
//					$string .= str_repeat("\010", strlen($this->progressBar) - $refreshLength) . str_repeat("\010", strlen($this->counter));
//					$string .= $this->progressBar;
//					$string .= $this->counter;
//
//					if ($this->numberOfTests > 60 && $this->currentTestNumber % 59 == 0)
//					{
//						$this->progressBar = '[' . str_pad(str_repeat('.', min(60, $this->numberOfTests - $this->currentTestNumber)), 60, '_', STR_PAD_RIGHT) . ']';
//						$this->counter = '';
//
//						$string .= "\n" . $this->progressBar;
//					}
//				}
//			}
//
//			$this->string = '';
//		}

		return $string;
	}

	public function refresh($refresh)
	{
		if ($this->numberOfTests > 0 && $this->currentTestNumber < $this->numberOfTests)
		{
			$this->refresh .= $refresh;
		}

		return $this;
	}
}

?>
