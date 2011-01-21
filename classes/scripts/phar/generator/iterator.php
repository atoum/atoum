<?php

namespace mageekguy\atoum\scripts\phar\generator;

class iterator extends \recursiveFilterIterator
{
	function accept()
	{
		return (substr($this->getInnerIterator()->current()->getFilename(), 0, 1) != '.');
	}
}

?>
