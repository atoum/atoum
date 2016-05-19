<?php

namespace mageekguy\atoum\report\fields\runner\event;

use
	mageekguy\atoum\test,
	mageekguy\atoum\runner,
	mageekguy\atoum\report,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\cli\progressBar
;

class cli extends report\fields\runner\event
{
	protected $progressBar = null;

	public function __construct(progressBar $progressBar = null)
	{
		parent::__construct();

		$this->progressBar = $progressBar ?: new progressBar();
	}

	public function __toString()
	{
		$string = '';

		if ($this->observable !== null)
		{
			if ($this->event === runner::runStop)
			{
				$string = PHP_EOL;
			}
			else
			{
				switch ($this->event)
				{
					case runner::runStart:
						$this->progressBar->reset()->setIterations($this->observable->getTestMethodNumber());
						break;

					case test::success:
						$this->progressBar->refresh('S');
						break;

					case test::fail:
						$this->progressBar->refresh('F');
						break;

					case test::void:
						$this->progressBar->refresh('0');
						break;

					case test::error:
						$this->progressBar->refresh('E');
						break;

					case test::exception:
						$this->progressBar->refresh('X');
						break;

					case test::uncompleted:
						$this->progressBar->refresh('U');
						break;

					case test::skipped:
						$this->progressBar->refresh('-');
						break;
				}

				$string = (string) $this->progressBar;
			}
		}

		return $string;
	}

	public function getProgressBar()
	{
		return $this->progressBar;
	}
}
