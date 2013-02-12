<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class libnotify extends notifier
{
	const command = 'notify-send -i %3$s %1$s %2$s';

	public function send($title, $message, $success)
	{
		return $this->execute(static::command, array($title, $message, static::getImage($success)));
	}

	private static function getImage($success)
	{
		return realpath(__DIR__ . '/../../../../../../resources/images/logo_' . ($success ? 'success' : 'fail') . '.png');
	}
}
