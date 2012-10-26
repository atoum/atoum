<?php

namespace mageekguy\atoum\report\fields\runner;

require_once __DIR__ . '/../../../../constants.php';

use
	mageekguy\atoum\runner,
	mageekguy\atoum\report
;

abstract class atoum extends report\field
{
	protected $author = null;
	protected $path = null;
	protected $version = null;

	public function __construct()
	{
		parent::__construct(array(runner::runStart));
	}

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

	public function handleEvent($event, \mageekguy\atoum\observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->author = \mageekguy\atoum\author;
			$this->path = $observable->getScore()->getAtoumPath();
			$this->version = $observable->getScore()->getAtoumVersion();

			return true;
		}
	}
}
