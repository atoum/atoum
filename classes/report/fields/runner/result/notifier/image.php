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
		$this->successImage = $path;

		return $this;
	}

	public function getSuccessImage()
	{
		return $this->successImage;
	}

	public function setFailureImage($path)
	{
		$this->failureImage = $path;

		return $this;
	}

	public function getFailureImage()
	{
		return $this->failureImage;
	}

	public function getImage($success)
	{
		$image = $success ? $this->getSuccessImage() : $this->getFailureImage();

		if ($this->getAdapter()->file_exists($image) === false)
		{
			throw new logic\invalidArgument(sprintf('File %s does not exist', $image));
		}

		return $image;
	}

	public function send($title, $message, $success)
	{
		return parent::send($title, $message, $this->getImage($success));
	}
}
