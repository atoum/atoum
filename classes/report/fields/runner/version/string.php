<?php

namespace mageekguy\atoum\report\fields\runner\version;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\version
{
	const titlePrompt = '> ';

	public function __toString()
	{
		return ($this->author === null || $this->version === null ? '' : self::titlePrompt . sprintf($this->locale->_('Atoum version %s by %s (%s).'), $this->version, $this->author, $this->path) . PHP_EOL);
	}
}

?>
