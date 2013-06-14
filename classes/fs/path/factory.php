<?php

namespace mageekguy\atoum\fs\path;

use
	mageekguy\atoum,
	mageekguy\atoum\fs\path
;

class factory
{
	protected $adapter = null;
	protected $directorySeparator = null;

	public function setDirectorySeparator($directorySeparator = null)
	{
		$this->directorySeparator = $directorySeparator;

		return $this;
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function build($path)
	{
		return new path($path, $this->directorySeparator, $this->adapter);
	}
}
