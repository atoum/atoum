<?php

namespace mageekguy\atoum\report\fields\test\event;

use
	\mageekguy\atoum,
	\mageekguy\atoum\cli,
	\mageekguy\atoum\report,
	\mageekguy\atoum\exceptions
;

class string extends report\fields\test\event
{
	public function __toString()
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
