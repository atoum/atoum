<?php

namespace mageekguy\atoum\report\fields\runner\php;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

abstract class path extends report\fields\runner
{
	protected $path = null;

	public function getPath()
	{
		return $this->path;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		$this->path = $runner->getScore()->getPhpPath();

		return $this;
	}
}

?>
