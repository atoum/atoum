<?php

namespace mageekguy\atoum\asserters;

class hash extends phpString
{
	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'issha1':
			case 'issha256':
			case 'issha512':
			case 'ismd5':
				return $this->{$asserter}();

			default:
				return parent::__get($asserter);
		}
	}

	public function isSha1($failMessage = null)
	{
		return $this->isHash(40, $failMessage);
	}

	public function isSha256($failMessage = null)
	{
		return $this->isHash(64, $failMessage);
	}

	public function isSha512($failMessage = null)
	{
		return $this->isHash(128, $failMessage);
	}

	public function isMd5($failMessage = null)
	{
		return $this->isHash(32, $failMessage);
	}

	protected function isHash($length, $failMessage = null)
	{
		if (strlen($this->valueIsSet()->value) === $length)
		{
			 $this->matches('/^[a-fA-F0-9]+$/', $failMessage ?: $this->_('%s does not match given pattern', $this));
		}
		else
		{
			$this->fail($failMessage ?: $this->_('%s should be a string of %d characters', $this, $length));
		}

		return $this;
	}
}
