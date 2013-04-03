<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier\image;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\report\fields\runner\result\notifier\image
;

class growl extends image
{
	 protected $callbackUrl = null;

	protected function getCommand()
	{
		return 'growlnotify --title %s --name atoum --message %s --image %s --url %s';
	}

	public function setCallbackUrl($url)
	{
		$this->callbackUrl = $url;

		return $this;
	}

	public function send($title, $message, $success)
	{
		return $this->adapter->system(sprintf($this->getCommand(), escapeshellarg($title), escapeshellarg($message), escapeshellarg($this->getImage($success)), escapeshellarg($this->callbackUrl)));
	}
}
