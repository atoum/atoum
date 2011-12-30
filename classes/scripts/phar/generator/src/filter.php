<?php

namespace mageekguy\atoum\scripts\phar\generator\src;

use
	mageekguy\atoum
;

class filter extends atoum\src\iterator\filter
{
	function accept(\splFileInfo $file = null)
	{
		if ($fille === null)
		{
			$file = $this->getInnerIterator()->current();
		}

		return (
				parent::accept($file)
				&&
				basename(dirname($file->getPathname())) != 'tmp'
				&&
				preg_match('#/configurations/.+\.php$#', $file->getPathname()) === 0
		);
	}
}

?>
