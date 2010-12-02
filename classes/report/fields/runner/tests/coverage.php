<?php

namespace mageekguy\atoum\report\fields\runner\tests;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class coverage extends report\fields\runner
{
	const titlePrompt = '> ';

	protected $coverage = null;

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($event === atoum\runner::runStop)
		{
			$this->coverage = $runner->getScore()->getCoverage();
		}

		return $this;
	}

	public function getCoverage()
	{
		return $this->coverage;
	}

	public function toString()
	{
		$string = parent::toString();

		if ($this->coverage !== null && sizeof($this->coverage) > 0)
		{
			$string .= self::titlePrompt;
		}

		return $string;
	}
}

?>
