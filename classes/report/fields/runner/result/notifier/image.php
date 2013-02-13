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
	protected $successImage = null;
	protected $failureImage = null;

	public function setSuccessImage($path)
	{
		if ($this->getAdapter()->file_exists($path) === false)
		{
			throw new logic\invalidArgument(sprintf('File %s does not exist', $path));
		}

		$this->successImage = $path;

		return $this;
	}

	public function getSuccessImage()
	{
		return $this->successImage;
	}

	public function setFailureImage($path)
	{
		if ($this->getAdapter()->file_exists($path) === false)
		{
			throw new logic\invalidArgument(sprintf('File %s does not exist', $path));
		}

		$this->failureImage = $path;

		return $this;
	}

	public function getFailureImage()
	{
		return $this->failureImage;
	}

	protected function getImage($success)
	{
		return $success ? $this->getSuccessImage() : $this->getFailureImage();
	}
}
