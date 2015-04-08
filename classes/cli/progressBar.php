<?php

namespace mageekguy\atoum\cli;

use
	mageekguy\atoum
;

class progressBar
{
	const width = 60;
	const defaultProgressBarFormat = '[%s]';
	const defaultCounterFormat = '[%s/%s]';

	protected $cli = null;
	protected $refresh = null;
	protected $progressBar = null;
	protected $progressBarFormat = null;
	protected $counter = null;
	protected $counterFormat = null;
	protected $iterations = 0;
	protected $currentIteration = 0;

	public function __construct($iterations = 0, atoum\cli $cli = null)
	{
		$this->iterations = $iterations;
		$this->progressBarFormat = self::defaultProgressBarFormat;
		$this->counterFormat = self::defaultCounterFormat;

		$this->setCli($cli ?: new atoum\cli());
	}

	public function reset()
	{
		$this->refresh = null;
		$this->iterations = 0;
		$this->currentIteration = 0;
		$this->progressBar = null;
		$this->counter = null;

		return $this;
	}

	public function setIterations($iterations)
	{
		$this->reset()->iterations = (int) $iterations;

		return $this;
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
			$this->progressBar = sprintf($this->progressBarFormat, ($this->iterations > self::width ? str_repeat('.', self::width - 1) . '>' : str_pad(str_repeat('.', $this->iterations), self::width, '_', STR_PAD_RIGHT)));

			$this->counter = sprintf($this->counterFormat, sprintf('%' . strlen((string) $this->iterations) . 'd', $this->currentIteration), $this->iterations);

			$string .= $this->progressBar . $this->counter;
		}

		if ($this->refresh !== null)
		{
			$refreshLength = strlen($this->refresh);

			$this->currentIteration += $refreshLength;

			if ($this->cli->isTerminal() === false)
			{
				$this->progressBar = substr($this->progressBar, 0, $this->currentIteration) . $this->refresh . substr($this->progressBar, $this->currentIteration + 1);
				$string .= PHP_EOL . $this->progressBar;
			}
			else
			{
				$string .= str_repeat("\010", (strlen($this->progressBar) - $refreshLength) + strlen($this->counter));
				$this->progressBar = $this->refresh . substr($this->progressBar, $refreshLength + 1);
				$string .= $this->progressBar;
			}

			$this->counter = sprintf($this->counterFormat, sprintf('%' . strlen((string) $this->iterations) . 'd', $this->currentIteration), $this->iterations);

			$string .= $this->counter;

			if ($this->iterations > self::width && $this->iterations - $this->currentIteration && $this->currentIteration % (self::width - 1) == 0)
			{
				$this->progressBar = '[' . (($this->iterations - $this->currentIteration) > (self::width - 1) ? str_repeat('.', self::width - 1) . '>' : str_pad(str_repeat('.', $this->iterations - $this->currentIteration), self::width, '_', STR_PAD_RIGHT)) . ']';
				$this->counter = '';

				$string .= PHP_EOL . $this->progressBar;
			}

			$this->refresh = null;
		}

		return $string;
	}

	public function refresh($value)
	{
		if ($this->iterations > 0 && $this->currentIteration < $this->iterations)
		{
			$this->refresh .= $value;
		}

		return $this;
	}
}
