<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class libnotify extends notifier
{
	const command = 'notify-send -i %3$s %1$s %2$s';

	protected $directory = null;

	public function send($title, $message, $success)
	{
		return $this->execute(static::command, array($title, $message, $this->getImage($success)));
	}

	public function setDirectory($directory)
	{
		if ($this->getAdapter()->is_dir($directory) === false)
		{
			throw new logic\invalidArgument(sprintf('Directory %s does not exist', $directory));
		}

		$this->directory = $directory;

		return $this;
	}

	public function getDirectory()
	{
		return $this->directory;
	}

	private function getImage($success)
	{
		return $this->directory . DIRECTORY_SEPARATOR . ($success ? 'success' : 'fail') . '.png';
	}
}
