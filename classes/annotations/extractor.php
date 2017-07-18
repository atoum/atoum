<?php

namespace mageekguy\atoum\annotations;

class extractor
{
	protected $handlers = array();

	public function extract($comments)
	{
		$comments = trim((string) $comments);

		if (substr($comments, 0, 2) == '/*' && substr($comments, -2) == '*/')
		{
			$comments = preg_replace('#^\/\*+([^*])#', '\1', $comments);
			$comments = preg_replace('#([^*])\*+/$#', '\1', $comments);
			$comments = trim($comments);

			foreach (preg_split("/\r?\n/", $comments) as $comment)
			{
				$cleanComment = ltrim($comment, "* \t\r\n\0\x0B");

				if (preg_match('/^@/', $cleanComment) === 0) {
				    continue;
				}

				$cleanComment = ltrim($cleanComment, "@");

				if ($cleanComment != $comment)
				{
					$comment = preg_split("/\s+/", $cleanComment);

					if ($comment)
					{
						$annotation = strtolower($comment[0]);

						switch (sizeof($comment))
						{
							case 1:
								$value = true;
								break;

							case 2:
								$value = $comment[1];
								break;

							default:
								$value = join(' ', array_slice($comment, 1));
						}

						foreach ($this->handlers as $handlerAnnotation => $handlerValue)
						{
							if ($annotation == strtolower($handlerAnnotation))
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
		switch (strtolower((string) $value))
		{
			case 'on':
			case '1':
			case 'true':
				return true;

			default:
				return false;
		}
	}

	public static function toArray($value)
	{
		return array_values(array_unique(preg_split('/\s+/', $value)));
	}
}
