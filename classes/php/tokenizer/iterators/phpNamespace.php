<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	mageekguy\atoum\php\tokenizer,
	mageekguy\atoum\php\tokenizer\iterators
;

class phpNamespace extends tokenizer\iterator
{
	protected $constants = array();
	protected $functions = array();
	protected $classes = array();

	public function reset()
	{
		$this->functions = array();
		$this->constants = array();
		$this->classes = array();

		return parent::reset();
	}

	public function getConstants()
	{
		return $this->constants;
	}

	public function getConstant($index)
	{
		return (isset($this->constants[$index]) === false ? null : $this->constants[$index]);
	}

	public function appendConstant(iterators\phpConstant $phpConstant)
	{
		$this->constants[] = $phpConstant;

		return $this->append($phpConstant);
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

	public function getFunctions()
	{
		return $this->functions;
	}

	public function getFunction($index)
	{
		return (isset($this->functions[$index]) === false ? null : $this->functions[$index]);
	}

	public function appendFunction(iterators\phpFunction $phpFunction)
	{
		$this->functions[] = $phpFunction;

		return $this->append($phpFunction);
	}
}
