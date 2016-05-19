<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

class phpArgument extends tokenizer\iterator
{
	protected $defaultValue = null;

	public function getDefaultValue()
	{
		return $this->defaultValue;
	}

	public function appendDefaultValue(iterators\phpDefaultValue $phpDefaultValue)
	{
		$this->defaultValue = $phpDefaultValue;

		return $this->append($phpDefaultValue);
	}
}
