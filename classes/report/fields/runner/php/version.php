<?php

namespace mageekguy\atoum\report\fields\runner\php;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\runner
;

abstract class version extends report\field
{
	protected $version = null;

	public function __construct()
	{
		parent::__construct(array(runner::runStart));
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			$this->version = $observable->getScore()->getPhpVersion();

			return true;
		}
	}
}
