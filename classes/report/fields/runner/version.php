<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class version extends report\fields\runner
{
	const titlePrompt = '> ';

	protected $author = null;
	protected $number = null;

	public function getAuthor()
	{
		return $this->author;
	}

	public function getNumber()
	{
		return $this->number;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($event === atoum\runner::runStart)
		{
			$this->author = atoum\test::author;
			$this->number = atoum\test::getVersion();
		}

		return $this;
	}

	public function __toString()
	{
		return ($this->author === null || $this->number === null ? '' : self::titlePrompt . sprintf($this->locale->_('Atoum version %s by %s.'), $this->number, $this->author) . PHP_EOL);
	}
}

?>
