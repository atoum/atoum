<?php

namespace mageekguy\atoum\reporters\cli;

use \mageekguy\atoum;

class progressBar extends atoum\reporter
{
	const width = 60;
	const progressBarFormat = '[%s]';
	const counterFormat = '[%s]';

	protected $numberOfTests = 0;
	protected $currentTestNumber = 0;
	protected $progressBar = null;
	protected $counter = null;
	protected $refresh = null;

	public function __construct(atoum\test $test)
	{
		$this->numberOfTests = sizeof($test);
	}

	public function __toString()
	{
		$string = '';

		if ($this->progressBar === null && $this->counter === null)
		{
			$this->progressBar = sprintf(self::progressBarFormat, ($this->numberOfTests > self::width ?  str_repeat('.', self::width - 1) . '>' : str_pad(str_repeat('.', $this->numberOfTests), self::width, '_', STR_PAD_RIGHT)));

			$this->counter = '[' . sprintf('%' . strlen((string) $this->numberOfTests) . 'd', $this->currentTestNumber) . '/' . $this->numberOfTests . ']';

			$string .= $this->progressBar . $this->counter;
		}

		if ($this->refresh !== null)
		{
			$refreshLength = strlen($this->refresh);

			$this->currentTestNumber += $refreshLength;

			$string .= str_repeat("\010", strlen($this->progressBar) - $refreshLength) . str_repeat("\010", strlen($this->counter));

			$this->progressBar = $this->refresh . substr($this->progressBar, $refreshLength + 1);
			$this->counter = '[' . sprintf('%' . strlen((string) $this->numberOfTests) . 'd', $this->currentTestNumber) . '/' . $this->numberOfTests . ']';

			$string .= $this->progressBar;
			$string .= $this->counter;

			if ($this->numberOfTests > self::width && $this->currentTestNumber % (self::width - 1) == 0)
			{
				$this->progressBar = '[' . str_pad(str_repeat('.', min(self::width, $this->numberOfTests - $this->currentTestNumber)), self::width, '_', STR_PAD_RIGHT) . ']';
				$this->counter = '';

				$string .= "\n" . $this->progressBar;
			}

			$this->refresh = null;
		}

		return $string;
	}

	public function refresh($value)
	{
		if ($this->numberOfTests > 0 && $this->currentTestNumber < $this->numberOfTests)
		{
			$this->refresh .= $value;
		}

		return $this;
	}
}

?>
