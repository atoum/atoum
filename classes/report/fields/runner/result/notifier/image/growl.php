<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier\image;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\report\fields\runner\result\notifier\image
;

class growl extends image
{
	const command = 'growlnotify --title %s --name atoum --message %s --image %s';

	protected function send($title, $message, $success)
	{
		$output = null;
		$this->execute(static::command, array($title, $message, $this->getImage($success)));

		return $output;
	}

	protected function getImage($success)
	{
		return $this->directory . DIRECTORY_SEPARATOR . ($success ? 'success' : 'fail') . '.png';
	}
}
