<?php

namespace mageekguy\atoum\report\decorators;

use \mageekguy\atoum\report;

class string extends report\decorator
{
	public function write(report\field $field)
	{
		return $this->flush($field);
	}

	public function flush(report\field $field)
	{
		return $this->sendToWriters((string)$field);
	}
}

?>
