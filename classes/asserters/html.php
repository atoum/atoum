<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs,
	mageekguy\atoum\tests\functional\selenium
;

class html extends atoum\asserter
{
	protected $value = null;
	protected $isSet = false;

	public function __toString()
	{
		return $this->getTypeOf($this->value);
	}

	public function toString()
	{
		return $this->generator->castToString($this->valueIsSet()->value);
	}

	public function setWith($value)
	{
		$this->value = $value;
		$this->isSet = true;

		if (self::isHtml($this->value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not an instance of selenium\html'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function wasSet()
	{
		return ($this->isSet === true);
	}

	public function reset()
	{
		$this->value = null;
		$this->isSet = false;
		$this->isSetByReference = false;

		return $this;
	}

	public function getValue()
	{
		return $this->value;
	}

	protected function valueIsSet($message = 'Value is undefined')
	{
		if ($this->isSet === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	public function hasTitle($title, $failMessage = null)
	{
		if ($this->valueIsSet()->value->getTitle() == $title)
		{
			$this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(
				($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('%s is not equal to %s'), $this->getTypeOf($this->value->getTitle()), $this->getTypeOf($title))) .
				PHP_EOL .
				$diff->setReference($title)->setData($this->value->getTitle())
			);
		}

		return $this;
	}

	protected static function check($value, $method)
	{
		if (self::isHtml($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an instance of selenium\html');
		}
	}

	protected static function isHtml($value)
	{
		return ($value instanceof selenium\html);
	}
}

?>
