<?php

namespace atoum\reports\realtime;

use
	atoum\reports,
	atoum\report\fields
;

class tap extends reports\realtime
{
	public function __construct()
	{
		parent::__construct();

		$this
			->addField(new fields\runner\tap\plan())
			->addField(new fields\test\event\tap())
		;
	}
}
