<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

abstract class php extends report\fields\runner
{
	protected $phpPath = null;
	protected $phpVersion = null;

	public function getPhpPath()
	{
		return $this->phpPath;
	}

	public function getPhpVersion()
	{
		return $this->phpVersion;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		$score = $runner->getScore();

		$this->phpPath = $score->getPhpPath();
		$this->phpVersion = $score->getPhpVersion();

		return $this;
	}
}

?>
