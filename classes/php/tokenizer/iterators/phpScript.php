<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

class phpScript extends tokenizer\iterators\phpNamespace
{
	protected $namespaces = array();
	protected $importations = array();

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

	public function appendImportation(iterators\phpImportation $phpImportation)
	{
		$this->importations[] = $phpImportation;

		return $this->append($phpImportation);
	}

	public function getImportations()
	{
		return $this->importations;
	}

	public function getImportation($index)
	{
		return (isset($this->importations[$index]) === false ? null : $this->importations[$index]);
	}
}
