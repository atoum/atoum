<?php

namespace mageekguy\atoum;

use \mageekguy\atoum\template;
use \mageekguy\atoum\exceptions;

class template extends template\block
{
	public function setParent(template\block $parent)
	{
		throw new exceptions\logic('Root template can not have any parent');
	}

	public function isRoot()
	{
		return true;
	}
}

?>
