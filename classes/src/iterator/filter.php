<?php

namespace mageekguy\atoum\src\iterator;

use
	mageekguy\atoum
;

class filter extends \recursiveFilterIterator
{
	function accept(\splFileInfo $file = null)
	{
		if ($file === null)
		{
			$file = $this->current();
		}

		return (substr($file->getFilename(), 0, 1) != '.');
	}
}

?>
