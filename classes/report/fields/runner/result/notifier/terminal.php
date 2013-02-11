<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class terminal extends notifier
{
	public function send($title, $message, $success)
	{
		return $this->execute(static::getCommand(), array($title, $message));
	}

	private static function getCommand()
	{
		return 'terminal-notifier -title %s -message %s';
	}
}
