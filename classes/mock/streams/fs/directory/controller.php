<?php

namespace mageekguy\atoum\mock\streams\fs\directory;

use
	mageekguy\atoum\exceptions,
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
}
