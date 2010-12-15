<?php

namespace mageekguy\atoum\report\fields\runner\version;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\version
{
	const titlePrompt = '> ';

	public function __toString()
	{
		return ($this->author === null || $this->number === null ? '' : self::titlePrompt . sprintf($this->locale->_('Atoum version %s by %s.'), $this->number, $this->author) . PHP_EOL);
	}
}

?>
