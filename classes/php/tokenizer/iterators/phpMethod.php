<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

class phpMethod extends tokenizer\iterator
{
	protected $arguments = array();

	public function appendArgument(iterators\phpArgument $phpArgument)
	{
		$this->arguments[] = $phpArgument;

		return $this->append($phpArgument);
	}

	public function getArguments()
	{
		return $this->arguments;
	}
}

?>
