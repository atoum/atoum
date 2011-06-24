<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

class phpScript extends tokenizer\iterators\phpNamespace
{
	protected $namespaces = array();

	public function reset()
	{
		$this->namespaces = array();

		return parent::reset();
	}

	public function getNamespaces()
	{
		return $this->namespaces;
	}

	public function getNamespace($index)
	{
		return (isset($this->namespaces[$index]) === false ? null : $this->namespaces[$index]);
	}

	public function appendNamespace(iterators\phpNamespace $phpNamespace)
	{
		$this->namespaces[] = $phpNamespace;

		return $this->append($phpNamespace);
	}
}

?>
