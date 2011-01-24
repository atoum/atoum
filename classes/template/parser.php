<?php

namespace mageekguy\atoum\template;

namespace \mageekguy\atoum;

class parser
{
	const defaultNamespace = 'tpl';
	const eol = "\n";

	private $namespace = '';

	public function __construct($namespace = self::defaultNamespace)
	{
		$this->setNamespace($namespace);
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function setNamespace($namespace)
	{
		if (is_string($namespace) == false)
		{
			trigger_error('Namespace must be a string', E_USER_ERROR);
		}
		else
		{
			$this->namespace = $namespace;
			return $this;
		}
	}

	public function checkString($string, & $error)
	{
		return $this->parse($string, new atoum\template(), $error);
	}

	public function checkFile($file, & $error)
	{
		$error = null;

		if ($file->exists() === true)
		{
			$error = 'Path \'' . $file->getPath() . '\' is not a file';
			return false;
		}
		else if ($file->isReadable() === false)
		{
			$error = 'Unable to read file \'' . $file->getRealPath() . '\'';
			return false;
		}
		else
		{
			$file->read($string);
			return $this->checkString($string, $error);
		}
	}

	public function parseString($string, parent $parent = null)
	{
		if (is_string($string) == false)
		{
			trigger_error('Argument must be a string', E_USER_ERROR);
		}
		else if ($this->parse($string, $parent, $error) == true)
		{
			return $parent;
		}
		else
		{
			trigger_error($error, E_USER_ERROR);
		}
	}

	public function parseFile(\ogo\fs\file $file, parent $parent = null)
	{
		if ($file->exists() == false)
		{
			trigger_error('Path \'' . $file->getPath() . '\' is not a file', E_USER_ERROR);
		}
		else
		{
			$file->read($string);

			if ($this->parse($string, $parent, $error) == true)
			{
				return $parent;
			}
			else
			{
				trigger_error($file->getPath() . ': ' . $error, E_USER_ERROR);
			}
		}
	}

	public static function getTemplateFromString($string, parent $parent = null, $namespace = self::defaultNamespace)
	{
		$templateParser = new self($namespace);

		return $templateParser->parseString($string, $parent);
	}

	public static function getTemplateFromFile(\ogo\fs\file $file, parent $parent = null, $namespace = self::defaultNamespace)
	{
		$templateParser = new self($namespace);

		return $templateParser->parseFile($file, $parent);
	}

	private function parse($string, & $root, & $error)
	{
		if ($root === null)
		{
			$root = new atoum\template();
		}

		$currentTemplateTag = $root;
		$error = null;
		$stack = array();
		$offset = 1;
		$line = 1;

		while (preg_match('%<(/)?' . $this->namespace . ':([^\s/>]+)(?(1)\s*|((?:\s+\w+="[^"]*")*)\s*(/?))(>)%', $string, $tag, PREG_OFFSET_CAPTURE) == true)
		{
			if ($tag[0][1] != 0)
			{
				$data = substr($string, 0, $tag[0][1]);

				$lastEol = strrpos($data, self::eol);

				if ($lastEol === false)
				{
					$offset += strlen($data);
				}
				else
				{
					$line += substr_count($data, self::eol);
					$offset = strlen(substr($data, $lastEol));
				}

				$currentTemplateTag->addChild(new data($data));
			}

			$string = substr($string, $tag[5][1] + 1);

			if ($tag[1][0] == '') # < /> or < > tag
			{
				$child = new tag($tag[2][0], null, $line, $offset);

				$currentTemplateTag->addChild($child);

				if (preg_match_all('%(\w+)="([^"]*)"%', $tag[3][0], $attributes) == true)
				{
					foreach ($attributes[1] as $index => $attribute)
					{
						if ($this->setAttribute($child, $attribute, $attributes[2][$index], $error) == false)
						{
							$error = 'Line ' . $line . ' at offset ' . $offset . ' : ' . $error;
							return false;
						}
					}
				}

				if ($tag[4][0] == '') # < >
				{
					$stack[] = $child;
					$currentTemplateTag = $child;
				}
			}
			else # </ >
			{
				$stackedTemplateTag = array_pop($stack);

				if ($stackedTemplateTag === null || $stackedTemplateTag->getTag() != $tag[2][0])
				{
					$error = 'Line ' . $line . ' at offset ' . $offset . ' : Tag \'' . $tag[2][0] . '\' is not open';
					return false;
				}
				else
				{
					$currentTemplateTag = end($stack);

					if ($currentTemplateTag === false)
					{
						$currentTemplateTag = $root;
					}
				}
			}

			$offset += ($tag[5][1] - $tag[0][1]) + 1;
		}

		if ($string != '')
		{
			$currentTemplateTag->addChild(new data($string));
			$offset += strlen($string);
		}

		if (sizeof($stack) == 0)
		{
			return true;
		}
		else
		{
			$error = 'Line ' . $line . ' at offset ' . $offset . ' : Tag \'' . $currentTemplateTag->getTag() . '\' must be closed';
			return false;
		}
	}

	private function setAttribute(tag $tag, $attribute, $value, & $error)
	{
		$error = '';

		switch ($attribute)
		{
			case 'html':
				if ($this->setHtml($tag, $value) == true)
				{
					return true;
				}
				else
				{
					$error = 'Value \'' . $value . '\' of html attribute is invalid';
					return false;
				}

			case 'id':
				$tagWithSameId = $tag->getById($value);

				if ($tagWithSameId === null)
				{
					$tag->setId($value);
					return true;
				}
				else
				{
					$error = 'Id \'' . $value . '\' is already define at line ' . $tagWithSameId->getLine() . ' at offset ' . $tagWithSameId->getOffset();
					return false;
				}

			default:
				$error = 'Attribute \'' . $attribute . '\' is invalid';
				return false;
		}
	}

	private function setHtml(tag $tag, $value)
	{
		switch ($value)
		{
			case 'true':
				$tag->enableHtml();
				return true;

			case 'false':
				$tag->disableHtml();
				return true;

			default:
				return false;
		}
	}

	private static function getOffset($data)
	{
		$lastEol = strrpos($data, "\n");

		if ($lastEol === false)
		{
			$lastEol = 0;
		}

		return strlen(substr($data, $lastEol)) + 1;
	}
}

?>
