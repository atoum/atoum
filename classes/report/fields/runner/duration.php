<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class duration extends report\fields\runner
{
	const titlePrompt = '> ';

	protected $value = null;

	public function getValue()
	{
		return $this->value;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($event === atoum\runner::runStop)
		{
			$this->value = $runner->getRunningDuration();
		}

		return $this;
	}

	public function toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Running duration: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $this->value), $this->value);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>
