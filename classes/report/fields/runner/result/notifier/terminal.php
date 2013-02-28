<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class terminal extends notifier
{
	public function getCommand()
	{
		return 'terminal-notifier -title %s -message %s';
	}
}
