<?php

namespace mageekguy\atoum\report\fields\runner;

require_once(__DIR__ . '/../../../../constants.php');

use
	mageekguy\atoum\runner,
	mageekguy\atoum\report
;

abstract class atoum extends report\fields\runner
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

	public function setWithRunner(runner $runner, $event = null)
	{
		if ($event === runner::runStart)
		{
			$this->author = \mageekguy\atoum\author;
			$this->path = $runner->getScore()->getAtoumPath();
			$this->version = $runner->getScore()->getAtoumVersion();
		}

		return $this;
	}
}

?>
