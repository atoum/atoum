<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\report\fields\runner\result\notifier
;

class growl extends notifier
{
	const command = 'growlnotify --title %s --name atoum --message %s --image %s';

	protected $directory = null;

	protected function send($title, $message, $success)
	{
		$output = null;
		$this->execute(static::command, array($title, $message, $this->getImage($success)));

		return $output;
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

	protected function getImage($success)
	{
		return $this->directory . DIRECTORY_SEPARATOR . ($success ? 'success' : 'fail') . '.png';
	}
}
