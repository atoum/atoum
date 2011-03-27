<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

abstract class version extends report\fields\runner
{
	protected $author = null;
	protected $path = null;
	protected $version = null;

	public function getAuthor()
	{
		return $this->author;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($event === atoum\runner::runStart)
		{
			$this->author = atoum\test::author;
			$this->path = realpath(dirname($runner->getPath()) . DIRECTORY_SEPARATOR . '..');
			$this->version = atoum\test::getVersion();
		}

		return $this;
	}
}

?>
