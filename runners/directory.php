<?php

namespace mageekguy\atoum\runners\directory;

use \mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

class directoryFilter extends \recursiveFilterIterator
{
	function accept()
	{
		return (substr($this->getInnerIterator()->current()->getFilename(), 0, 1) != '.');
	}
}


if (PHP_SAPI === 'cli' && realpath($_SERVER['argv'][0]) === __FILE__)
{
	foreach (array_slice($_SERVER['argv'], 1) as $path)
	{
		if (is_dir($path) === true)
		{
			foreach (new \recursiveIteratorIterator(new directoryFilter(new \recursiveDirectoryIterator($path))) as $file)
			{
				require($file->getPathname());
			}
		}
	}
}

?>
