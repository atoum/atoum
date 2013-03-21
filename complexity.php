<?php

require __DIR__ . '/classes/autoloader.php';

use mageekguy\atoum;

$rootData = array('name' => 'atoum', 'children' => array());

$tokenFilter = function($token) {
	if (is_array($token) === true)
	{
		switch ($token[0])
		{
			case T_WHITESPACE:
			case T_COMMENT:
			case T_DOC_COMMENT:
				return false;

			default:
				return true;
		}
	}

	return true;
};

foreach (new recursiveIteratorIterator(new atoum\iterators\filters\recursives\atoum\source(__DIR__)) as $file)
{
	$data = & $rootData;

	$directories = ltrim(substr(dirname($file->getPathname()), strlen(__DIR__)), DIRECTORY_SEPARATOR);

	if ($directories !== '')
	{
		foreach (explode(DIRECTORY_SEPARATOR, $directories) as $directory)
		{
			$childFound = false;

			foreach ($data['children'] as $key => $child)
			{
				if ($child['name'] === $directory)
				{
					$childFound = true;
					break;
				}
			}

			if ($childFound === false)
			{
				$key = sizeof($data['children']);
				$data['children'][] = array(
					'name' => $directory,
					'children' => array()
				);
			}

			$data = & $data['children'][$key];
		}
	}

	$data['children'][] = array(
		'name' => $file->getFilename(),
		'size' => sizeof(array_filter(token_get_all(file_get_contents($file)), $tokenFilter))
	);
}

file_put_contents(__DIR__ . '/atoum.json', json_encode($rootData));
