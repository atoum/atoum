<?php
namespace mageekguy\atoum\filesystem;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream
;

class filesystem extends directory
{
	private $test;

	public function __construct(atoum\test $test, $name = null) {
		parent::__construct($name);

		$this->test = $test;
	}

	public function getParent() {
		return $this->test;
	}
}
