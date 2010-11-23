<?php

namespace mageekguy\atoum\score;

use \mageekguy\atoum\exceptions;

class coverage
{
	protected $lines = array();
	protected $reflectionClassInjector = null;

	public function __construct() {}

	public function getLines()
	{
		return $this->lines;
	}

	public function getReflectionClass($class)
	{
		$reflectionClass = null;

		if ($this->reflectionClassInjector === null)
		{
			$reflectionClass = new \reflectionClass($class);
		}
		else
		{
			$reflectionClass = $this->reflectionClassInjector->__invoke($class);

			if ($reflectionClass instanceof \reflectionClass === false)
			{
				throw new exceptions\runtime\unexpectedValue('Reflection class injector must return a \reflectionClass instance');
			}
		}

		return $reflectionClass;
	}

	public function setReflectionClassInjector(\closure $reflectionClassInjector)
	{
		$closure = new \reflectionMethod($reflectionClassInjector, '__invoke');

		if ($closure->getNumberOfParameters() !== 1)
		{
			throw new exceptions\logic\argument('Reflection class injector must take one argument');
		}

		$this->reflectionClassInjector = $reflectionClassInjector;

		return $this;
	}
}

?>
