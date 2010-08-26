<?php

namespace mageekguy\atoum\asserters;

class collection extends \mageekguy\atoum\asserters\variable
{
	public function isEmpty()
	{
		sizeof($this->mixed) == 0 ? $this->pass() : $this->fail($this->locale->_('Collection is not empty'));

		return $this;
	}

	public function isNotEmpty()
	{
		sizeof($this->mixed) > 0 ? $this->pass() : $this->fail($this->locale->_('Collection is empty'));

		return $this;
	}

	protected static function check($mixed, $method)
	{
		if (is_array($mixed) === false)
		{
			throw new \logicException('Argument of ' . $method . '() must be an array');
		}
	}
}

?>
