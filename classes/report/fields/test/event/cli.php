<?php

namespace mageekguy\atoum\report\fields\test\event;

use
	mageekguy\atoum\test,
	mageekguy\atoum\report,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\cli\progressBar
;

class cli extends report\fields\test\event
{
	protected $progressBar = null;

	public function __construct(progressBar $progressBar = null, atoum\locale $locale = null)
	{
		parent::__construct($locale);

		$this->progressBar = $progressBar ?: new progressBar();
	}

	public function getProgressBar()
	{
		return $this->progressBar;
	}

	public function __toString()
	{
		$string = '';

		if ($this->observable !== null)
		{
			switch ($this->event)
			{
				case test::runStop:
				case test::runtimeException:
					$string = PHP_EOL;
					break;

				default:
					switch ($this->event)
					{
						case test::runStart:
							$this->progressBar->reset()->setIterations(sizeof($this->observable));
							break;

						case test::success:
							$this->progressBar->refresh('S');
							break;

						case test::fail:
							$this->progressBar->refresh('F');
							break;

						case test::error:
							$this->progressBar->refresh('e');
							break;

						case test::exception:
							$this->progressBar->refresh('E');
							break;

						case test::uncompleted:
							$this->progressBar->refresh('U');
							break;

						case test::runStop:
							$this->progressBar->reset();
							break;
					}

					$string = (string) $this->progressBar;
			}
		}

		return $string;
	}
}

?>
