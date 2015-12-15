<?php

namespace mageekguy\atoum\tools\variable;

class analyzer
{
	public function getTypeOf($mixed)
	{
		switch (gettype($mixed))
		{
			case 'boolean':
				return sprintf('boolean(%s)', $mixed == false ? 'false' : 'true');

			case 'integer':
				return sprintf('integer(%s)', $mixed);

			case 'double':
				return sprintf('float(%s)', $mixed);

			case 'NULL':
				return 'null';

			case 'object':
				return sprintf('object(%s)', get_class($mixed));

			case 'resource':
				return sprintf('%s of type %s', $mixed, get_resource_type($mixed));

			case 'string':
				return sprintf('string(%s) \'%s\'', strlen($mixed), $mixed);

			case 'array':
				return sprintf('array(%s)', sizeof($mixed));
		}
	}

	public function dump($mixed)
	{
		ob_start();

		var_dump($mixed);

		return trim(ob_get_clean());
	}

	public function isObject($mixed)
	{
		return (is_object($mixed) === true);
	}

	public function isException($mixed)
	{
		return ($mixed instanceof \exception);
	}

	public function isBoolean($mixed)
	{
		return (is_bool($mixed) === true);
	}

	public function isInteger($mixed)
	{
		return (is_int($mixed) === true);
	}

	public function isFloat($mixed)
	{
		return (is_float($mixed) === true);
	}

	public function isString($mixed)
	{
		return (is_string($mixed) === true);
	}

	public function isUtf8($mixed)
	{
		return ($this->isString($mixed) === true && preg_match('/^.*$/us', $mixed) === 1);
	}

	public function isArray($mixed)
	{
		return (is_array($mixed) === true);
	}

	public function isResource($mixed)
	{
		return (is_resource($mixed) === true);
	}

	public function isRegex($namespace)
	{
		return false !== @preg_match($namespace, null);
	}

	public function isValidIdentifier($identifier)
	{
		return 0 !== \preg_match('#^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$#', $identifier);
	}

	public function isValidNamespace($namespace)
	{
		foreach(explode('\\', trim($namespace, '\\')) as $sub)
		{
			if (!self::isValidIdentifier($sub))
			{
				return false;
			}
		}

		return true;
	}
}
