<?php

namespace atoum\report\fields\runner;

require_once __DIR__ . '/../../../../constants.php';

use
	atoum\runner,
	atoum\report
;

abstract class atoum extends report\field
{
	protected $author = null;
	protected $path = null;
	protected $version = null;

	public function __construct(\atoum\locale $locale = null)
	{
		parent::__construct(array(runner::runStart), $locale);
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

	public function handleEvent($event, \atoum\observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->author = \atoum\author;
			$this->path = $observable->getScore()->getAtoumPath();
			$this->version = $observable->getScore()->getAtoumVersion();

			return true;
		}
	}
}
