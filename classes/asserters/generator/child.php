<?php

namespace mageekguy\atoum\asserters\generator;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class child extends asserters\variable {
	private $parent;

	public function __construct(asserters\generator $parent = null)
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

	public function setWithParent(asserters\generator $parent)
	{
		$this->parent = $parent;

		return $this;
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

