<?php

namespace mageekguy\atoum\tools;

class diff
{
	protected $expected = null;
	protected $actual = null;
	protected $diff = null;
	protected $decorator = null;

	public function __construct($expected = null, $actual = null)
	{
		$this->setDecorator();

		if ($expected !== null)
		{
			$this->setExpected($expected);
		}

		if ($actual !== null)
		{
			$this->setActual($actual);
		}
	}

	public function __invoke($expected = null, $actual = null)
	{
		$this->make($expected, $actual);

		return $this;
	}

	public function __toString()
	{
		return $this->decorator->decorate($this);
	}

	public function setDecorator(diff\decorator $decorator = null)
	{
		$this->decorator = $decorator ?: new diff\decorator();
		return $this;
	}

	public function getDecorator()
	{
		return $this->decorator;
	}

	public function setExpected($mixed)
	{
		$this->expected = (string) $mixed;
		$this->diff = null;

		return $this;
	}

	public function getExpected()
	{
		return $this->expected;
	}

	public function setActual($mixed)
	{
		$this->actual = (string) $mixed;
		$this->diff = null;

		return $this;
	}

	public function getActual()
	{
		return $this->actual;
	}

	public function make($expected = null, $actual = null)
	{
		if ($expected !== null)
		{
			$this->setExpected($expected);
		}

		if ($expected !== null)
		{
			$this->setActual($actual);
		}

		if ($this->diff === null)
		{
			$this->diff = $this->diff(self::split($this->expected), self::split($this->actual));
		}

		return $this->diff;
	}

	protected function diff($old, $new)
	{
		$diff = array();

		if (sizeof($old) > 0 || sizeof($new) > 0)
		{
			$lengths = array();
			$maxLength = 0;

			foreach ($old as $oldKey => $oldValue)
			{
				$newKeys = array_keys($new, $oldValue);

				foreach ($newKeys as $newKey)
				{
					$lengths[$oldKey][$newKey] = isset($lengths[$oldKey - 1][$newKey - 1]) === false ? 1 : $lengths[$oldKey - 1][$newKey - 1] + 1;

					if ($lengths[$oldKey][$newKey] > $maxLength)
					{
						$maxLength = $lengths[$oldKey][$newKey];
						$oldMaxLength = $oldKey + 1 - $maxLength;
						$newMaxLength = $newKey + 1 - $maxLength;
					}
				}
			}

			if ($maxLength == 0)
			{
				$diff = array(array('-' => $old, '+' => $new));
			}
			else
			{
				$diff = array_merge(
					$this->diff(array_slice($old, 0, $oldMaxLength), array_slice($new, 0, $newMaxLength)),
					array_slice($new, $newMaxLength, $maxLength),
					$this->diff(array_slice($old, $oldMaxLength + $maxLength), array_slice($new, $newMaxLength + $maxLength))
				);
			}
		}

		return $diff;
	}

	protected static function split($value)
	{
		return explode(PHP_EOL, $value);
	}
}
