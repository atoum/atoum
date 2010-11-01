<?php

namespace mageekguy\atoum\report\fields\runner\durations;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class running extends report\fields\runner
{
	protected $duration = null;

	public function toString()
	{
		return
			$this->duration === null ?
			$this->locale->_('Running duration: unknown.') :
			sprintf($this->locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $this->duration), $this->duration)
		;
	}

	public function getDuration()
	{
		return $this->duration;
	}

	public function setWithRunner(atoum\runner $runner)
	{
		$this->duration = $runner->getRunningDuration();

		return $this;
	}
}

?>
