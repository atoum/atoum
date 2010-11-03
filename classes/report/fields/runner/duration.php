<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class duration extends report\fields\runner
{
	protected $value = null;

	public function toString()
	{
		return
			$this->value === null ?
			$this->locale->_('Running duration: unknown.') :
			sprintf($this->locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $this->value), $this->value)
		;
	}

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
}

?>
