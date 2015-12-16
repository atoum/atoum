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

	public function matches($type, $failMessage = null)
	{
		$pattern = '/^' . $type . '$/i';
		$actualType = get_resource_type($this->valueIsSet()->value);

		if (0 !== preg_match($pattern, $actualType))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s does not match %s', $this, $type));
		}

		return $this;
	}

	public function __call($name, $arguments)
	{
		if ('is' === substr($name, 0, 2)) {
			$pattern = preg_replace(['/^is/', '/_/'], ['', '.?'], $name);
			$pattern = preg_replace_callback(
				'/([A-Z])([a-z]+)/',
				function ($matches) {
					return '.?' . strtolower($matches[1]) . $matches[2];
				},
				$pattern
			);

			if (1 === count($arguments)) {
				return $this->matches($pattern, $arguments[0]);
			} else {
				return $this->matches($pattern);
			}
		}

		return parent::__call($name, $arguments);
	}
}
