<?php

namespace mageekguy\atoum\test\annotations;

use mageekguy\atoum;

class extractor extends atoum\annotations\extractor
{
	protected $handlers = array();

	public function extract($comments)
	{
		foreach (parent::extract($comments) as $annotation => $value)
		{
			foreach ($this->handlers as $handlerAnnotation => $handlerValue)
			{
				if (strtolower($annotation) == strtolower($handlerAnnotation))
				{
					call_user_func_array($handlerValue, array($value));
				}
			}
		}

		return $this;
	}

	public function setHandler($annotation, \closure $handler)
	{
		$this->handlers[$annotation] = $handler;

		return $this;
	}

	public function unsetHandler($annotation)
	{
		if (isset($this->handlers[$annotation]) === true)
		{
			unset($this->handlers[$annotation]);
		}

		return $this;
	}

	public function getHandlers()
	{
		return $this->handlers;
	}

	public function resetHandlers()
	{
		$this->handlers = array();

		return $this;
	}

	public static function toBoolean($value)
	{
		return strcasecmp($value, 'on') == 0;
	}

	public static function toArray($value)
	{
		return array_values(array_unique(preg_split('/\s+/', $value)));
	}
}

?>
