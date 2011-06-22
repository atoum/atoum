<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

class phpScript extends tokenizer\iterator
{
	protected $classes = array();
	protected $namespaces = array();

	public function reset()
	{
		$this->classes = array();
		$this->namespaces = array();

		return parent::reset();
	}

	public function getClasses()
	{
		return $this->classes;
	}

	public function getClass($index)
	{
		return (isset($this->classes[$index]) === false ? null : $this->classes[$index]);
	}

	public function appendClass(iterators\phpClass $phpClass)
	{
		$this->classes[] = $phpClass;

		return $this->append($phpClass);
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
