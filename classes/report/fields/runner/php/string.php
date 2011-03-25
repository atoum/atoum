<?php

namespace mageekguy\atoum\report\fields\runner\php;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\php
{
	const titlePrompt = '> ';

	public function __toString()
	{
		return self::titlePrompt . sprintf($this->locale->_('PHP version: %s'), $this->phpVersion) . PHP_EOL .	self::titlePrompt . sprintf($this->locale->_('PHP path: %s'), $this->phpPath) . PHP_EOL;
	}
}

?>
