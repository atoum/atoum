<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

class phpNamespace extends tokenizer\iterator
{
	protected $classes = array();

	public function getClasses()
	{
		return $this->classes;
	}

	public function appendClass(iterators\phpClass $phpClass)
	{
		$this->classes[] = $phpClass;

		return $this->append($phpClass);
	}
}

?>
