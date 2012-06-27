<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\exceptions
;

class hash extends string
{
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
			 $this->match('/^[a-f0-9]+$/', sprintf($this->getLocale()->_('%s does not match given pattern'), $this));
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s should be a string of %d characters'), $this, $length));
		}

		return $this;
	}
}
