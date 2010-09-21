<?php

namespace mageekguy\atoum\phar;

class iterator extends \recursiveFilterIterator
{
	function accept()
	{
		return (substr($this->getInnerIterator()->current()->getFilename(), 0, 1) != '.');
	}
}

?>
