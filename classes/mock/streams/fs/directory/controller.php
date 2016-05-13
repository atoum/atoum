<?php

namespace mageekguy\atoum\mock\streams\fs\directory;

use
	mageekguy\atoum\mock\streams\fs
;

class controller extends fs\controller
{
	public function __construct($path)
	{
		parent::__construct($path);

		$this->setPermissions('755');
	}

	public function setPermissions($permissions)
	{
		return parent::setPermissions(0400000 | octdec($permissions));
	}

	public function getContents()
	{
		return array();
	}

	public function mkdir($path, $mode, $options)
	{
		if ($this->exists === true)
		{
			return false;
		}
		else
		{
			$this->setPermissions($mode)->exists = true;

			return true;
		}
	}

	public function rmdir($path, $options)
	{
		if ($this->exists === false || $this->checkIfWritable() === false)
		{
			return false;
		}
		else
		{
			$this->exists = false;

			return true;
		}
	}

	public function dir_opendir($path, $useSafeMode)
	{
		return $this->exists;
	}

	public function dir_closedir()
	{
		return $this->exists;
	}
}
