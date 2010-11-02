<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class version extends report\fields\runner
{
	protected $author = null;
	protected $number = null;

	public function __construct(atoum\locale $locale = null)
	{
		parent::__construct($locale);

		$this->author = atoum\test::author;
		$this->number = atoum\test::getVersion();
	}

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
		return $this;
	}

	public function toString()
	{
		return sprintf($this->locale->_('Atoum version %s by %s.'), $this->number, $this->author);
	}
}

?>
