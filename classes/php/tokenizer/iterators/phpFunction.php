<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

class phpFunction extends tokenizer\iterator
{
	protected $arguments = array();

	public function reset()
	{
		$this->arguments = array();

		return parent::reset();
	}

	public function appendArgument(iterators\phpArgument $phpArgument)
	{
		$this->arguments[] = $phpArgument;

		return $this->append($phpArgument);
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function getArgument($index)
	{
		return (isset($this->arguments[$index]) === false ? null : $this->arguments[$index]);
	}
}

?>
