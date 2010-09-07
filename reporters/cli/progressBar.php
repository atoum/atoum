<?php

namespace mageekguy\atoum\reporters\cli;

use \mageekguy\atoum;

class progressBar extends atoum\reporter
{
	protected $size = 0;
	protected $update = 0;
	protected $string = '';

	public function __construct(atoum\test $test)
	{
		$this->size = sizeof($test);

		$this->string = '[';

		if ($this->size > 60)
		{
			$this->string .= str_repeat('.', 59) . '>';
		}
		else
		{
			$this->string .= str_repeat('.', $this->size);

			if ($this->size < 60)
			{
				$this->string .= str_repeat('_', 60 - $this->size);
			}
		}

		$this->string .= '][' . sprintf('%' . strlen((string) $this->size) . 'd', 0) . '/' . $this->size . ']';
	}

	public function __toString()
	{
		return $this->string;
	}

	public function update($value)
	{
		if ($this->size > 0)
		{
			$this->update++;

			$this->string = str_repeat("\010", strlen($this->string) - $this->update) . $value;

			if ($this->size > 60)
			{
				$this->string .= str_repeat('.', 59 - $this->update) . '>';
			}
			else
			{
				$this->string .= str_repeat('.', $this->size - $this->update);

				if ($this->size < 60)
				{
					$this->string .= str_repeat('_', 60 - $this->size);
				}
			}

			$this->string .= '][' . sprintf('%' . strlen((string) $this->size) . 'd', $this->update) . '/' . $this->size . ']';
		}

		return $this;
	}
}

?>
