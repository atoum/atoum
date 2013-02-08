<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class terminal extends notifier
{
	protected static function notify($title, $message, $success)
	{
		$output = null;
		exec(
			sprintf(
				static::getCommand(),
				escapeshellarg(__DIR__ . '/../../../../../../resources/images/logo_' . ($success ? 'success' : 'fail') . '.png'),
				escapeshellarg($title),
				escapeshellarg($message)
			),
			$output
		);

		return $output ?: '';
	}

	private static function getCommand()
	{
		return 'notify-send -t %s %s %s';
	}
}
