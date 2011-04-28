<?php

namespace mageekguy\atoum\scripts;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class tagger extends atoum\script
{
	protected $sourceDirectory = null;

	public function getSourceDirectory()
	{
		return $this->sourceDirectory;
	}

	public function setSourceDirectory($directory)
	{
		$this->sourceDirectory = (string) $directory;

		return $this;
	}

	public function getFilesIterator()
	{
		if ($this->sourceDirectory === null)
		{
			throw new exceptions\logic('Unable to get files iterator, source directory is undefined');
		}
	}
}

?>
