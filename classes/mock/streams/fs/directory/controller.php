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

		$this->setMode('755');
	}

	public function setMode($mode)
	{
		return parent::setMode(0400000 | octdec($mode));
	}

	public function getContents()
	{
		return array();
	}
}
