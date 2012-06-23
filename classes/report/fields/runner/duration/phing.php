<?php

namespace mageekguy\atoum\report\fields\runner\duration;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\duration
;

class phing extends duration\cli
{
	public function __toString()
	{
		return
			$this->prompt .
			sprintf(
				$this->locale->_('%1$s: %2$s.'),
				$this->titleColorizer->colorize($this->locale->_('Running duration')),
				$this->durationColorizer->colorize($this->value === null ? $this->locale->_('unknown') : sprintf($this->locale->__('%4.2f second', '%4.2f seconds', $this->value), $this->value)
			)
		);
	}
}
