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
		$actualType = get_resource_type($this->valueIsSet()->value);

		if ($actualType === $type)
		{
			$this->pass();
		}
		else
		{
			$this->fail(($failMessage ?: $this->_('%s is not of type %s', $this, $type)) . PHP_EOL . $this->diff($actualType));
		}

		return $this;
	}
}
