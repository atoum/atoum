<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier\image;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier\image
;

class libnotify extends image
{
	const command = 'notify-send -i %3$s %1$s %2$s';

	public function send($title, $message, $success)
	{
		return $this->execute(static::command, array($title, $message, $this->getImage($success)));
	}

	protected function getImage($success)
	{
		return $this->directory . DIRECTORY_SEPARATOR . ($success ? 'success' : 'fail') . '.png';
	}
}
