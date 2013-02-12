<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class terminal extends notifier
{
	const command = 'terminal-notifier -title %s -message %s';

	public function send($title, $message, $success)
	{
		return $this->execute(static::command, array($title, $message));
	}
}
