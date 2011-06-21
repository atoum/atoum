<?php

namespace mageekguy\atoum\php\tokenizer\iterators;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

class phpClass extends tokenizer\iterator
{
	protected $methods = array();
	protected $properties = array();

	public function getMethods()
	{
		return $this->methods;
	}

	public function appendMethod(iterators\phpMethod $phpMethod)
	{
		$this->methods[] = $phpMethod;

		return $this->append($phpMethod);
	}

	public function getProperties()
	{
		return $this->properties;
	}

	public function appendProperty(iterators\phpProperty $phpProperty)
	{
		$this->properties[] = $phpProperty;

		return $this->append($phpProperty);
	}
}

?>
