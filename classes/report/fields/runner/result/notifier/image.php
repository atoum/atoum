<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\report\fields\runner\result\notifier
;

abstract class image extends notifier
{
	protected $directory = null;

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

	abstract protected function getImage($success);
}
