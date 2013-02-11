<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class growl extends notifier
{
	protected function send($title, $message, $success)
	{
		$output = null;
		$this->execute(static::getCommand(), array($title, $message, static::getImage($success)));

		return $output;
	}

	private static function getCommand()
	{
		return 'growlnotify --title %s --name atoum --message %s --image %s';
	}

	private static function getImage($success)
	{
		return realpath(__DIR__ . '/../../../../../../resources/images/logo_' . ($success ? 'success' : 'fail') . '.png');
	}
}
