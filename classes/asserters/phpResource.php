<?php

namespace mageekguy\atoum\asserters;

class phpResource extends variable
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

	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'type':
				return $this->getTypeAsserter();

			default:
				return $this->generator->__get($asserter);
		}
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
			$this->fail($failMessage ?: $this->_('%s is not of type %s', $this, $type));
		}

		return $this;
	}

	protected function matches($pattern, $failMessage = null)
	{
		$actualType = get_resource_type($this->valueIsSet()->value);

		if (0 !== preg_match($pattern, $actualType))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s does not match %s', $this, $pattern));
		}

		return $this;
	}

	public function __call($name, $arguments)
	{
		if ('is' === substr($name, 0, 2))
		{
			$pattern = preg_replace(array('/^is/', '/_/'), array('', '.?'), $name);
			$pattern = preg_replace_callback(
				'/([A-Z])([a-z]+)/',
				function ($matches) {
					return '.?' . strtolower($matches[1]) . $matches[2];
				},
				$pattern
			);
			$pattern = '/^' . $pattern . '$/i';

			if (1 === count($arguments))
			{
				return $this->matches($pattern, $arguments[0]);
			}

			return $this->matches($pattern);
		}

		return parent::__call($name, $arguments);
	}

	protected function getTypeAsserter()
	{
		return $this->generator->__call('phpString', array(get_resource_type($this->valueIsSet()->value)));
	}
}
