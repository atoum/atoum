<?php

namespace mageekguy\atoum\cli;

use
	mageekguy\atoum
;

class progressBar
{
	const width = 60;
	const progressBarFormat = '[%s]';
	const counterFormat = '[%s]';

	protected $cli = null;
	protected $refresh = null;
	protected $counter = null;
	protected $testsNumber = 0;
	protected $progressBar = null;
	protected $currentTestNumber = 0;

	public function __construct(atoum\test $test, atoum\cli $cli = null)
	{
		$this->testsNumber = sizeof($test);

		$this->setCli($cli ?: new atoum\cli());
	}

	public function setCli(atoum\cli $cli)
	{
		$this->cli = $cli;

		return $this;
	}

	public function getCli()
	{
		return $this->cli;
	}

	public function __toString()
	{
		$string = '';

		if ($this->progressBar === null && $this->counter === null)
		{
			$this->progressBar = sprintf(self::progressBarFormat, ($this->testsNumber > self::width ?  str_repeat('.', self::width - 1) . '>' : str_pad(str_repeat('.', $this->testsNumber), self::width, '_', STR_PAD_RIGHT)));

			$this->counter = '[' . sprintf('%' . strlen((string) $this->testsNumber) . 'd', $this->currentTestNumber) . '/' . $this->testsNumber . ']';

			$string .= $this->progressBar . $this->counter;
		}

		if ($this->refresh !== null)
		{
			$refreshLength = strlen($this->refresh);

			$this->currentTestNumber += $refreshLength;

			if ($this->cli->isTerminal() === false)
			{
				$this->progressBar = substr($this->progressBar, 0, $this->currentTestNumber) . $this->refresh . substr($this->progressBar, $this->currentTestNumber + 1);
				$string .= PHP_EOL . $this->progressBar;
			}
			else
			{
				$string .= str_repeat("\010", (strlen($this->progressBar) - $refreshLength) + strlen($this->counter));
				$this->progressBar = $this->refresh . substr($this->progressBar, $refreshLength + 1);
				$string .= $this->progressBar;
			}

			$this->counter = '[' . sprintf('%' . strlen((string) $this->testsNumber) . 'd', $this->currentTestNumber) . '/' . $this->testsNumber . ']';

			$string .= $this->counter;

			if ($this->testsNumber > self::width && $this->currentTestNumber % (self::width - 1) == 0)
			{
				$this->progressBar = '[' . str_pad(str_repeat('.', min(self::width, $this->testsNumber - $this->currentTestNumber)), self::width, '_', STR_PAD_RIGHT) . ']';
				$this->counter = '';

				$string .= PHP_EOL . $this->progressBar;
			}

			$this->refresh = null;
		}

		return $string;
	}

	public function refresh($value)
	{
		if ($this->testsNumber > 0 && $this->currentTestNumber < $this->testsNumber)
		{
			$this->refresh .= $value;
		}

		return $this;
	}
}

?>
