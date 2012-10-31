<?php

namespace mageekguy\atoum\template;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\template\parser
;

class parser
{
	const eol = "\n";
	const defaultNamespace = 'tpl';

	protected $namespace = '';
	protected $adapter = null;
	protected $errorLine = null;
	protected $errorOffset = null;
	protected $errorMessage = null;

	public function __construct($namespace = null, atoum\adapter $adapter = null)
	{
		$this
			->setNamespace($namespace ?: self::defaultNamespace)
			->setAdapter($adapter ?: new atoum\adapter())
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

	public function checkString($string)
	{
		return $this->parse($string);
	}

	public function checkFile($path)
	{
		return $this->checkString($this->getFileContents($path));
	}

	public function parseString($string, atoum\template $root = null)
	{
		$this->parse((string) $string, $root);

		return $root;
	}

	public function parseFile($path, atoum\template $root = null)
	{
		$this->parse($this->getfileContents($path), $root);

		return $root;
	}

	protected function parse($string, & $root = null)
	{
		if ($root === null)
		{
			$root = new atoum\template();
		}

		$currentTag = $root;

		$stack = array();

		$line = 1;
		$offset = 1;

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
						try
						{
							$child->setAttribute($attribute, $attributes[2][$index]);
						}
						catch (\exception $exception)
						{
							throw new parser\exception($exception->getMessage(), $line, $offset, $exception);
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
					throw new parser\exception('Tag \'' . $tag[2][0] . '\' is not open', $line, $offset);
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

		if (sizeof($stack) > 0)
		{
			throw new parser\exception('Tag \'' . $currentTag->getTag() . '\' must be closed', $line, $offset + strlen($string));
		}

		return $this;
	}

	protected function getFileContents($path)
	{
		$fileContents = $this->adapter->file_get_contents($path);

		if ($fileContents === false)
		{
			throw new exceptions\runtime('Unable to get contents from file \'' . $path . '\'');
		}

		return $fileContents;
	}
}
