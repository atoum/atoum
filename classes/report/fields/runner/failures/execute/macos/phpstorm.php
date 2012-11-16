<?php

namespace mageekguy\atoum\report\fields\runner\failures\execute\macos;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\adapter,
	mageekguy\atoum\report\fields\runner\failures
;

class phpstorm extends failures\execute
{
	public function __construct($command = null)
	{
		$command = $command ?: '/Applications/PhpStorm.app/Contents/MacOS/webide';

		parent::__construct($command);
	}

	public function setCommand($command)
	{
		return parent::setCommand($command . ' --line %2$d %1$s &> /dev/null &');
	}
}
