<?php

namespace mageekguy\atoum\annotations;

class extractor
{
	protected $handlers = array();

	public function extract($comments)
	{
		$comments = trim((string) $comments);

		if (substr($comments, 0, 3) == '/**' && substr($comments, -2) == '*/')
		{
			foreach (explode("\n", trim(trim($comments, '/*'))) as $comment)
			{
				$comment = trim(trim(trim($comment), '*/'));

				if (substr($comment, 0, 1) == '@')
				{
					$comment = preg_split("/\s+/", $comment);

					$sizeofComment = sizeof($comment);

					if ($sizeofComment >= 2)
					{
						$annotation = substr($comment[0], 1);
						$value = $sizeofComment == 2 ? $comment[1] : join(' ', array_slice($comment, 1));

						foreach ($this->handlers as $handlerAnnotation => $handlerValue)
						{
							if (strtolower($annotation) == strtolower($handlerAnnotation))
							{
								call_user_func_array($handlerValue, array($value));
							}
						}
					}
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
