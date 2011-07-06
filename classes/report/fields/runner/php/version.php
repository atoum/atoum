<?php

namespace mageekguy\atoum\report\fields\runner\php;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

abstract class version extends report\fields\runner
{
	protected $version = null;

	public function getVersion()
	{
		return $this->version;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		$this->version = $runner->getScore()->getPhpVersion();

		return $this;
	}
}

?>
