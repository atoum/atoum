<?php

namespace mageekguy\atoum\php;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer
;

class tokenizer implements \iteratorAggregate
{
	protected $iterator = null;

	public function __construct()
	{
		$this->iterator = new tokenizer\iterator();
	}

	public function getIterator()
	{
		return $this->iterator;
	}

	public function resetIterator()
	{
		$this->iterator->reset();

		return $this;
	}

	public function tokenize($string)
	{
		foreach (token_get_all($string) as $token)
		{
			$this->iterator->append(new tokenizer\token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));
		}

		return $this;
	}
}

?>
