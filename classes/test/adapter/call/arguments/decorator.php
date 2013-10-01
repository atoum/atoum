<?php

namespace mageekguy\atoum\test\adapter\call\arguments;

class decorator
{
	public function decorate(array $arguments = null)
	{
		$string = '';

		if ($arguments !== null && sizeof($arguments) > 0)
		{
			$string = array();

			foreach ($arguments as $argument)
			{
				switch ($type = gettype($argument))
				{
					case 'boolean':
						$string[] = ($argument ? 'TRUE' : 'FALSE');
						break;

					case 'integer':
						$string[] = 'integer(' . $argument . ')';
						break;

					case 'double':
						$string[] = 'float(' . $argument . ')';
						break;

					case 'string':
						$string[] = 'string(' . strlen($argument) . ') "' . $argument . '"';
						break;

					case 'array':
						$string[] = 'array(' . ($size = sizeof($argument)) . ') {' . ($size <= 0 ? '' : '...') . '}';
						break;

					case 'object':
						$string[] = 'object(' . get_class($argument) . ')';
						break;

					case 'resource':
						ob_start();
						var_dump($argument);
						$string[] = ob_get_clean();
						break;

					case 'NULL':
						$string[] = $type;
						break;

					default:
						$string[] = $type;
				}
			}

			$string = join(', ', $string);
		}

		return $string;
	}
}
