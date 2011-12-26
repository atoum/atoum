<?php

namespace mageekguy\atoum\src\iterator;

use
	mageekguy\atoum
;

class filter extends \recursiveFilterIterator
{
	function accept()
	{
		$file = $this->getInnerIterator()->current();

		return (
				substr($file->getFilename(), 0, 1) != '.'
				&&
				basename(dirname($file->getPathname())) != 'tmp'
				&&
				preg_match('#/configurations/.+\.php$#', $file->getPathname()) === 0
		);
	}
}

?>
