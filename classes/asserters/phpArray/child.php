<?php

namespace mageekguy\atoum\asserters\phpArray;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class child extends asserters\phpArray {
	private $parent;

	public function __construct(asserters\phpArray $parent = null)
	{
		parent::__construct($parent->getGenerator(), $parent->getAnalyzer(), $parent->getLocale());

		$this->setWithParent($parent);
	}

	public function __get($property)
	{
		return $this->parentIsSet()->parent->__get($property);
	}

	public function __call($method, $arguments)
	{
		return $this->parentIsSet()->parent->__call($method, $arguments);
	}

	public function __invoke(\closure $assertions)
	{
		$assertions($this->parent->phpArray($this->value));

		return $this;
	}

	public function setWithParent(asserters\phpArray $parent)
	{
		$this->parent = $parent;

		return $this;
	}

	public function hasSize($size, $failMessage = null)
	{
		return $this->parentIsSet()->parent->hasSize($size, $failMessage);
	}

	public function isEmpty($failMessage = null)
	{
		return $this->parentIsSet()->parent->isEmpty($failMessage);
	}

	public function isNotEmpty($failMessage = null)
	{
		return $this->parentIsSet()->parent->isNotEmpty($failMessage);
	}

	public function strictlyContains($value, $failMessage = null)
	{
		return $this->parentIsSet()->parent->strictlyContains($value, $failMessage);
	}

	public function contains($value, $failMessage = null)
	{
		return $this->parentIsSet()->parent->contains($value, $failMessage);
	}

	public function strictlyNotContains($value, $failMessage = null)
	{
		return $this->parentIsSet()->parent->strictlyNotContains($value, $failMessage);
	}

	public function notContains($value, $failMessage = null)
	{
		return $this->parentIsSet()->parent->notContains($value, $failMessage);
	}

	public function hasKeys(array $keys, $failMessage = null)
	{
		return $this->parentIsSet()->parent->hasKeys($keys, $failMessage);
	}

	public function notHasKeys(array $keys, $failMessage = null)
	{
		return $this->parentIsSet()->parent->notHasKeys($keys, $failMessage);
	}

	public function hasKey($key, $failMessage = null)
	{
		return $this->parentIsSet()->parent->hasKey($key, $failMessage);
	}

	public function notHasKey($key, $failMessage = null)
	{
		return $this->parentIsSet()->parent->notHasKey($key, $failMessage);
	}

	public function containsValues(array $values, $failMessage = null)
	{
		return $this->parentIsSet()->parent->containsValues($values, $failMessage);
	}

	public function strictlyContainsValues(array $values, $failMessage = null)
	{
		return $this->parentIsSet()->parent->strictlyContainsValues($values, $failMessage);
	}

	public function notContainsValues(array $values, $failMessage = null)
	{
		return $this->parentIsSet()->parent->notContainsValues($values, $failMessage);
	}

	public function strictlyNotContainsValues(array $values, $failMessage = null)
	{
		return $this->parentIsSet()->parent->strictlyNotContainsValues($values, $failMessage);
	}

	public function isEqualTo($value, $failMessage = null)
	{
		return $this->parentIsSet()->parent->isEqualTo($value, $failMessage);
	}

	public function isNotEqualTo($value, $failMessage = null)
	{
		return $this->parentIsSet()->parent->isNotEqualTo($value, $failMessage);
	}

	public function isIdenticalTo($value, $failMessage = null)
	{
		return $this->parentIsSet()->parent->isIdenticalTo($value, $failMessage);
	}

	public function isNotIdenticalTo($value, $failMessage = null)
	{
		return $this->parentIsSet()->parent->isNotIdenticalTo($value, $failMessage);
	}

	public function isReferenceTo(& $reference, $failMessage = null)
	{
		return $this->parentIsSet()->parent->isReferenceTo($reference, $failMessage);
	}

	protected function containsValue($value, $failMessage, $strict)
	{
		return $this->parentIsSet()->parent->containsValue($value, $failMessage, $strict);
	}

	public function offsetGet($key)
	{
		$asserter = new child($this);

		return $asserter->setWith($this->valueIsSet()->value[$key]);
	}

	protected function parentIsSet()
	{
		if ($this->parent === null)
		{
			throw new exceptions\logic('Parent array asserter is undefined');
		}

		return $this;
	}
}

