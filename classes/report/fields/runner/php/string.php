<?php

namespace mageekguy\atoum\report\fields\runner\php;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\php
{
	const titlePrompt = '> ';
	const versionPrompt = '=> ';

	public function __toString()
	{
		return
				self::titlePrompt . sprintf($this->locale->_('PHP path: %s'), $this->phpPath) . PHP_EOL
			.	self::titlePrompt . $this->locale->_('PHP version:') . PHP_EOL . self::versionPrompt . str_replace(PHP_EOL, PHP_EOL . self::versionPrompt, rtrim($this->phpVersion)) . PHP_EOL
		;
	}
}

?>
