<?php

namespace mageekguy\atoum\iterators\filters\recursives;

use
	mageekguy\atoum
;

class dot extends \recursiveFilterIterator
{
	public function __construct($mixed)
	{
		if ($mixed instanceof \recursiveIterator)
		{
			parent::__construct($mixed);
		}
		else
		{
			parent::__construct($this->createFactory()->build('recursiveDirectoryIterator', array((string) $mixed)));
		}
	}

	public function accept()
	{
		return (substr(basename((string) $this->getInnerIterator()->current()), 0, 1) != '.');
	}

	public function createFactory()
	{
		return new atoum\factory();
	}
}
