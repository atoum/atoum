<?php

namespace mageekguy\atoum\path;

class rewriter
{
	protected $mapping = array();

	public function map($from, $to)
	{
		$this->mapping[self::cleanPath($from)] = self::cleanPath($to);

		uasort($this->mapping, function($path1, $path2) { return - strcmp($path1, $path2); });

		return $this;
	}

	public function getMapping()
	{
		return $this->mapping;
	}

	public function rewrite($path)
	{
		foreach ($this->mapping as $from => $to)
		{
			if (strpos($path, $from) === 0)
			{
				return $to . substr($path, strlen($from));
			}
		}

		return $path;
	}

	private static function cleanPath($path)
	{
		return rtrim((string) $path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}
}
