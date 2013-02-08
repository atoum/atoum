<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class growl extends notifier
{
	protected static function notify($title, $message, $success)
	{
		$output = null;
		exec(
			sprintf(
				static::getCommand(),
				escapeshellarg($title),
				escapeshellarg($message),
				escapeshellarg(__DIR__ . '/../../../../../../resources/images/logo_' . ($success ? 'success' : 'fail') . '.png')
			),
			$output
		);

		return $output ?: '';
	}

	private static function getCommand()
	{
		return 'growlnotify --title %s --name atoum --message %s --image %s';
	}
}
