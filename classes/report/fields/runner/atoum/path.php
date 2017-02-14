<?php

namespace mageekguy\atoum\report\fields\runner\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\runner
;

abstract class path extends report\field
{
	protected $path = null;

	public function __construct()
	{
		parent::__construct(array(runner::runStart));
	}

	public function getPath()
	{
		return $this->path;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}

		$this->path = realpath($_SERVER['argv'][0]);

		return true;
	}
}
