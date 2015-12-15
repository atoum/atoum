<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters
;

class phpResource extends asserters\variable
{
	public function setWith($value)
	{
		parent::setWith($value);

		if ($this->analyzer->isResource($this->value) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail($this->_('%s is not a resource', $this));
		}

		return $this;
	}

	public function isOfType($type, $failMessage = null)
	{
		if (get_resource_type($this->valueIsSet()->value) === $type)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s is not of type %s', $this, $type));
		}

		return $this;
	}
}
