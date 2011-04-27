<?php

namespace mageekguy\atoum\template;

use
	\mageekguy\atoum,
	\mageekguy\atoum\exceptions
;

class parser implements atoum\adapter\aggregator
{
	const defaultNamespace = 'tpl';
	const eol = "\n";

	protected $namespace = '';
	protected $adapter = null;

	public function __construct($namespace = self::defaultNamespace, atoum\adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}

		$this
			->setNamespace($namespace)
			->setAdapter($adapter)
		;
	}

	public function setNamespace($namespace)
	{
		$this->namespace = (string) $namespace;

		return $this;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	/*
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
	*/

	public function parseString($string, atoum\template $root = null)
	{
		if ($this->parse((string) $string, $root, $error) == true)
		{
			return $root;
		}
		else
		{
			throw new exceptions\runtime($error);
		}
	}

	/*
	public function parseFile($path, parent $parent = null)
	{
		$string = $this->adapter->file_get_contents($path);

		if ($string === false)
		{
		}

		if ($this->parse($string, $parent, $error) == true)
		{
			return $parent;
		}
		else
		{
			trigger_error($file->getPath() . ': ' . $error, E_USER_ERROR);
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
	*/

	private function parse($string, & $root, & $error)
	{
		if ($root === null)
		{
			$root = new atoum\template();
		}

		$currentTag = $root;
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

				$currentTag->addChild(new data($data));
			}

			$string = substr($string, $tag[5][1] + 1);

			if ($tag[1][0] == '') # < /> or < > tag
			{
				$child = new tag($tag[2][0], null, $line, $offset);

				$currentTag->addChild($child);

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
					$currentTag = $child;
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
					$currentTag = end($stack);

					if ($currentTag === false)
					{
						$currentTag = $root;
					}
				}
			}

			$offset += ($tag[5][1] - $tag[0][1]) + 1;
		}

		if ($string != '')
		{
			$currentTag->addChild(new data($string));
		}

		if (sizeof($stack) == 0)
		{
			return true;
		}
		else
		{
			$error = 'Line ' . $line . ' at offset ' . ($offset + strlen($string)) . ' : Tag \'' . $currentTag->getTag() . '\' must be closed';

			return false;
		}
	}

	private function setAttribute(tag $tag, $attribute, $value, & $error)
	{
		$attributeIsSet = true;

		$error = '';

		try
		{
			$tag->setAttribute($attribute, $value);
		}
		catch (\exception $exception)
		{
			$error = $exception->getMessage();
			$attributeIsSet = false;
		}

		return $attributeIsSet;
	}
}

?>
