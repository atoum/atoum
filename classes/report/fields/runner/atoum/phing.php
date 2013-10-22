<?php

namespace atoum\report\fields\runner\atoum;

use
	atoum\report
;

class phing extends report\fields\runner\atoum\cli
{
	public function __toString()
	{
		return ($this->author === null || $this->version === null ? '' : $this->prompt . $this->colorizer->colorize(sprintf($this->locale->_("Atoum version: %s \nAtoum path: %s \nAtoum author: %s"), $this->version, $this->path, $this->author)));
	}
}
