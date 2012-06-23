<?php

namespace mageekguy\atoum\tests\units\asserters\template\parser;

use
	mageekguy\atoum\asserters
;

class exception extends asserters\exception
{
	public function hasErrorLine($line, $failMessage = null)
	{
		if (self::isException($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('Line not found because %s is not an exception'), $this->value));
		}

		if ($this->value->getErrorLine() === $line)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Line is %s instead of %s'), $this->value->getErrorLine(), $line));
		}
	}

	public function hasErrorOffset($offset, $failMessage = null)
	{
		if (self::isException($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('Offset not found because %s is not an exception'), $this->value));
		}

		if ($this->value->getErrorOffset() === $offset)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Offset is %s instead of %s'), $this->value->getErrorOffset(), $offset));
		}
	}
}
