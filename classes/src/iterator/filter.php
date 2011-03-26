<?php

namespace mageekguy\atoum\src\iterator;

use \mageekguy\atoum;

class filter extends \recursiveFilterIterator
{
	function accept()
	{
		return (substr($this->getInnerIterator()->current()->getFilename(), 0, 1) != '.');
	}
}

?>
