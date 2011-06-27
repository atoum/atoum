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
	protected $namespaceImportations = array();

	public function reset()
	{
		$this->namespaces = array();

		return parent::reset();
	}

	public function appendNamespace(iterators\phpNamespace $phpNamespace)
	{
		$this->namespaces[] = $phpNamespace;

		return $this->append($phpNamespace);
	}

	public function getNamespaces()
	{
		return $this->namespaces;
	}

	public function getNamespace($index)
	{
		return (isset($this->namespaces[$index]) === false ? null : $this->namespaces[$index]);
	}

	public function appendNamespaceImportation(iterators\phpNamespace\importation $phpNamespaceImportation)
	{
		$this->namespaceImportations[] = $phpNamespaceImportation;

		return $this->append($phpNamespaceImportation);
	}

	public function getNamespaceImportations()
	{
		return $this->namespaceImportations;
	}

	public function getNamespaceImportation($index)
	{
		return (isset($this->namespaceImportations[$index]) === false ? null : $this->namespaceImportations[$index]);
	}
}

?>
