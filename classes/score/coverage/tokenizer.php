<?php

namespace mageekguy\atoum\score\coverage;

use mageekguy\atoum;
use mageekguy\atoum\score\coverage\tokenizer;

class tokenizer
{
	protected $adapter = null;
	protected $reflectionClassInjector = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}

		$this->setAdapter($adapter);
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getReflectionClass($className)
	{
		$reflectionClass = $this->reflectionClassInjector === null ? new \reflectionClass($className) : $this->reflectionClassInjector->__invoke($className);

		if ($reflectionClass instanceof \reflectionClass === false)
		{
			throw new tokenizer\exception('Reflection class injector must return a \reflectionClass instance');
		}

		return $reflectionClass;
	}

	public function setReflectionClassInjector(\closure $closure)
	{
		$this->reflectionClassInjector = $closure;

		return $this;
	}
}

?>
